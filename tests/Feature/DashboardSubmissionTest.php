<?php

use App\Livewire\Dashboard\Overview;
use App\Livewire\Dashboard\RegistrationDetail;
use App\Livewire\Dashboard\SubmissionDetail;
use App\Livewire\Dashboard\SubmissionIndex;
use Livewire\Livewire;

test('submission index route renders', function () {
    $this->get(route('dashboard.submission.index'))
        ->assertOk()
        ->assertSee('Manage your competition submissions and deadlines');
});

test('submission detail route renders for a configured stage', function () {
    $this->get(route('dashboard.submission.show', ['competition' => 'bpc', 'stage' => 'stage-1']))
        ->assertOk()
        ->assertSee('Business Plan Competition');
});

test('submission routes reject invalid competitions and stages', function () {
    $this->get('/dashboard/submission/invalid')->assertNotFound();
    $this->get('/dashboard/submission/bpc/invalid-stage')->assertNotFound();
});

test('no-registration submission index shows its empty state', function () {
    Livewire::test(SubmissionIndex::class)
        ->assertSee('No submissions yet')
        ->assertSee('View Registration');
});

test('unverified competitions remain visible but locked', function () {
    Livewire::test(SubmissionIndex::class)
        ->set('scenario', 'waiting_verification')
        ->assertSee('Business Case Competition')
        ->assertSee('Registration verification required')
        ->assertSee('Locked');
});

test('mixed submission index shows open and submitted competition states', function () {
    Livewire::test(SubmissionIndex::class)
        ->set('scenario', 'mixed_submission')
        ->assertSee('Mini Case Competition')
        ->assertSee('Open')
        ->assertSee('Submitted');
});

test('upcoming and open submission states render their intended workspace', function () {
    Livewire::test(SubmissionDetail::class, ['competition' => 'bpc', 'stage' => 'stage-1'])
        ->set('scenario', 'upcoming')
        ->assertSee('Submission opens soon')
        ->set('scenario', 'open_empty')
        ->assertSee('Drop your files here')
        ->assertSee('Submission Requirements');
});

test('submission metadata validation identifies unsupported and oversized files', function () {
    Livewire::test(SubmissionDetail::class, ['competition' => 'bpc', 'stage' => 'stage-1'])
        ->call('addFiles', [[
            'id' => 'unsupported',
            'name' => 'notes.zip',
            'type' => 'application/zip',
            'size_bytes' => 1_000,
        ]])
        ->assertSee('Unsupported format: notes.zip')
        ->call('addFiles', [[
            'id' => 'oversized',
            'name' => 'deck.pdf',
            'type' => 'application/pdf',
            'size_bytes' => 6 * 1024 * 1024,
        ]])
        ->assertSee('File too large: deck.pdf');
});

test('submitted entries can become editable and be resubmitted in component-local state', function () {
    Livewire::test(SubmissionDetail::class, ['competition' => 'bpc', 'stage' => 'stage-1'])
        ->set('scenario', 'submitted')
        ->assertSee('Submission received')
        ->call('openUnsubmitConfirmation')
        ->assertSet('showUnsubmitConfirm', true)
        ->call('unsubmitEntry')
        ->assertSet('submissionState', 'editable')
        ->assertSee('editable again')
        ->call('openSubmitConfirmation')
        ->assertSet('showSubmitConfirm', true)
        ->call('submitEntry')
        ->assertSet('submissionState', 'submitted')
        ->assertSet('history.3.event', 'Resubmitted');
});

test('late and closed scenarios preserve lifecycle boundaries', function () {
    Livewire::test(SubmissionDetail::class, ['competition' => 'bpc', 'stage' => 'stage-1'])
        ->set('scenario', 'late')
        ->assertSee('Submitted late')
        ->assertSee('8 minutes late')
        ->set('scenario', 'closed_no_submission')
        ->assertSee('Submission closed')
        ->assertDontSee('Drop your files here')
        ->assertDontSee('Submit Entry');
});

test('overview and verified registration submission calls to action now resolve', function () {
    $submissionRoute = route('dashboard.submission.show', ['competition' => 'mcc', 'stage' => 'stage-1']);

    Livewire::test(Overview::class)
        ->set('scenario', 'active_participant')
        ->assertSee($submissionRoute);

    Livewire::test(RegistrationDetail::class, ['competition' => 'bpc'])
        ->set('scenario', 'verified')
        ->assertSee(route('dashboard.submission.show', ['competition' => 'bpc', 'stage' => 'stage-1']));
});
