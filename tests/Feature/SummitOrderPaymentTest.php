<?php

use App\Actions\Summit\CreateSummitOrder;
use App\Actions\Summit\RejectSummitOrder;
use App\Actions\Summit\SubmitSummitPaymentProof;
use App\Actions\Summit\VerifySummitOrder;
use App\Contracts\SummitPaymentStorage;
use App\Contracts\SummitTicketStorage;
use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Enums\UserRole;
use App\Models\PaymentSetting;
use App\Models\SummitOrder;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Tests\Fakes\FakeSummitPaymentStorage;
use Tests\Fakes\FakeSummitTicketStorage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $storage = new FakeSummitPaymentStorage;
    $this->app->instance(SummitPaymentStorage::class, $storage);
    $this->paymentStorage = $storage;

    $ticketStorage = new FakeSummitTicketStorage;
    $this->app->instance(SummitTicketStorage::class, $ticketStorage);
    $this->ticketStorage = $ticketStorage;

    PaymentSetting::query()->create([
        'contact_person_name' => 'Catalyst Contact',
        'contact_person_whatsapp' => '081200000000',
        'summit_ticket_price' => '150000.00',
        'is_active' => true,
    ]);
});

function summitOrderFixture(User $buyer): SummitOrder
{
    return app(CreateSummitOrder::class)->handle($buyer, ['Alya', 'Bima']);
}

function summitAdmin(): User
{
    return User::factory()->create([
        'email_verified_at' => now(),
        'role' => UserRole::Admin,
    ]);
}

test('a buyer can submit one proof and the order moves to verification', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = summitOrderFixture($buyer);

    $result = app(SubmitSummitPaymentProof::class)->handle(
        $buyer,
        $order,
        ' Buyer Sender ',
        UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg'),
    );

    expect($result->payment_status)->toBe(SummitOrderStatus::WaitingVerification)
        ->and($result->sender_name)->toBe('Buyer Sender')
        ->and($result->hasProof())->toBeTrue()
        ->and($result->tickets->pluck('status')->unique()->all())
        ->toBe([SummitTicketStatus::WaitingVerification])
        ->and($this->paymentStorage->uploaded)->toHaveCount(1);
});

test('a proof cannot be submitted twice for the same order', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = summitOrderFixture($buyer);
    $proof = fn () => UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg');

    app(SubmitSummitPaymentProof::class)->handle($buyer, $order, 'Sender', $proof());
    app(SubmitSummitPaymentProof::class)->handle($buyer, $order, 'Sender', $proof());
})->throws(ValidationException::class);

test('a proof must be a jpg jpeg or png no larger than five megabytes', function (UploadedFile $file) {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = summitOrderFixture($buyer);

    app(SubmitSummitPaymentProof::class)->handle($buyer, $order, 'Sender', $file);
})->with([
    'pdf' => fn () => UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf'),
    'too large' => fn () => UploadedFile::fake()->create('proof.png', 5121, 'image/png'),
])->throws(ValidationException::class);

test('an admin approval activates every ticket in the order', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = summitOrderFixture($buyer);
    app(SubmitSummitPaymentProof::class)->handle(
        $buyer, $order, 'Sender', UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg'),
    );

    $result = app(VerifySummitOrder::class)->handle(summitAdmin(), $order);

    expect($result->payment_status)->toBe(SummitOrderStatus::Verified)
        ->and($result->verified_by)->not->toBeNull()
        ->and($result->verified_at)->not->toBeNull()
        ->and($result->tickets->pluck('status')->unique()->all())->toBe([SummitTicketStatus::Active]);
});

test('a repeated approval is a harmless no-op', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = summitOrderFixture($buyer);
    app(SubmitSummitPaymentProof::class)->handle(
        $buyer, $order, 'Sender', UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg'),
    );
    $admin = summitAdmin();

    $first = app(VerifySummitOrder::class)->handle($admin, $order);
    $second = app(VerifySummitOrder::class)->handle($admin, $order);

    expect($second->id)->toBe($first->id)
        ->and($second->payment_status)->toBe(SummitOrderStatus::Verified)
        ->and($order->tickets()->count())->toBe(2);
});

test('an order without proof cannot be approved', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = summitOrderFixture($buyer);

    app(VerifySummitOrder::class)->handle(summitAdmin(), $order);
})->throws(ValidationException::class);

test('a rejection closes the order and every ticket', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = summitOrderFixture($buyer);
    app(SubmitSummitPaymentProof::class)->handle(
        $buyer, $order, 'Sender', UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg'),
    );

    $result = app(RejectSummitOrder::class)->handle(summitAdmin(), $order);

    expect($result->payment_status)->toBe(SummitOrderStatus::Rejected)
        ->and($result->tickets->pluck('status')->unique()->all())->toBe([SummitTicketStatus::Rejected]);
});

test('an approval generates one private pdf per ticket', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = summitOrderFixture($buyer);
    app(SubmitSummitPaymentProof::class)->handle(
        $buyer, $order, 'Sender', UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg'),
    );

    $result = app(VerifySummitOrder::class)->handle(summitAdmin(), $order);

    expect($this->ticketStorage->stored)->toHaveCount(2);

    foreach ($result->tickets as $ticket) {
        expect($ticket->pdf_url)->toContain('/catalyst/summit-tickets/')
            ->and($ticket->pdf_file_id)->not->toBeEmpty();
    }
});

test('a non-owner cannot submit proof for someone else order', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = summitOrderFixture($buyer);
    $outsider = User::factory()->create(['email_verified_at' => now()]);

    app(SubmitSummitPaymentProof::class)->handle(
        $outsider, $order, 'Sender', UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg'),
    );
})->throws(AuthorizationException::class);
