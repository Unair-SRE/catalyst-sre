<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-14">
        <div class="max-w-3xl space-y-8 lg:space-y-10">
            <header class="dashboard-page-heading">
                <p class="text-xs font-medium tracking-wide text-catalyst-muted">Account settings</p>
                <h1 class="mt-5 font-display text-3xl font-medium leading-tight tracking-tight text-catalyst-ink sm:text-4xl xl:text-5xl">Profile</h1>
                <p class="mt-4 text-base leading-7 text-catalyst-ink/80 sm:text-lg">Manage your personal information and account security.</p>
            </header>

            <form class="border-t border-catalyst-grey/30 bg-white pt-7 pb-2" wire:submit="saveProfile" novalidate>
                <div>
                    <h2 class="font-display text-xl font-semibold tracking-tight">Personal Information</h2>
                    <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">Keep your information accurate for registration and official Catalyst records.</p>
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium" for="profile-name">Full / Legal Name</label>
                        <input id="profile-name" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="text" autocomplete="name" wire:model="profile.name" @error('profile.name') aria-invalid="true" aria-describedby="profile-name-help profile-name-error" @else aria-describedby="profile-name-help" @enderror>
                        <p id="profile-name-help" class="mt-2 text-xs leading-5 text-catalyst-muted">Use your legal name. This name may be used for certificates and official Catalyst documents.</p>
                        @error('profile.name') <p id="profile-name-error" class="mt-1 text-sm text-status-error-ink">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium" for="profile-email">Email</label>
                        <input id="profile-email" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="email" autocomplete="email" wire:model="profile.email" @error('profile.email') aria-invalid="true" aria-describedby="profile-email-help profile-email-error" @else aria-describedby="profile-email-help" @enderror>
                        <p id="profile-email-help" class="mt-2 text-xs leading-5 text-catalyst-muted">{{ $state['email_notice'] }}</p>
                        @error('profile.email') <p id="profile-email-error" class="mt-1 text-sm text-status-error-ink">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium" for="profile-whatsapp">WhatsApp Number</label>
                        <input id="profile-whatsapp" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="tel" autocomplete="tel" wire:model="profile.whatsapp" @error('profile.whatsapp') aria-invalid="true" aria-describedby="profile-whatsapp-error" @enderror>
                        @error('profile.whatsapp') <p id="profile-whatsapp-error" class="mt-2 text-sm text-status-error-ink">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium" for="profile-institution">Institution / School / University</label>
                        <input id="profile-institution" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="text" autocomplete="organization" wire:model="profile.institution" @error('profile.institution') aria-invalid="true" aria-describedby="profile-institution-error" @enderror>
                        @error('profile.institution') <p id="profile-institution-error" class="mt-2 text-sm text-status-error-ink">{{ $message }}</p> @enderror
                    </div>
                </div>

                <p class="mt-6 border border-status-warning/30 bg-status-warning/5 p-4 text-sm leading-6 text-catalyst-ink/80">Some profile changes may affect active competition registrations. Production integration will decide which changes require review.</p>

                @if ($profileFeedback)
                    <p class="mt-5 text-sm font-medium text-status-success-ink" role="status">{{ $profileFeedback }}</p>
                @endif

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row">
                    <button class="inline-flex min-h-11 items-center justify-center border border-catalyst-grey/50 px-4 py-3 text-sm font-medium focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="resetProfile">Cancel Changes</button>
                    <button class="inline-flex min-h-11 items-center justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="submit">Save Changes</button>
                </div>
            </form>

            <form class="border-t border-catalyst-grey/30 bg-white pt-7 pb-2" wire:submit="updatePassword" novalidate>
                <div>
                    <h2 class="font-display text-xl font-semibold tracking-tight">Account Security</h2>
                    <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">Change Password</p>
                </div>

                <p class="mt-5 border border-status-info/30 bg-status-info/5 p-4 text-sm leading-6 text-catalyst-ink/80">This prototype validates form shape only. It does not check or update your real authentication password.</p>

                <div class="mt-6 space-y-5">
                    <div>
                        <label class="block text-sm font-medium" for="current-password">Current Password</label>
                        <input id="current-password" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="password" autocomplete="current-password" wire:model="password.current" @error('password.current') aria-invalid="true" aria-describedby="current-password-error" @enderror>
                        @error('password.current') <p id="current-password-error" class="mt-2 text-sm text-status-error-ink">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium" for="new-password">New Password</label>
                        <input id="new-password" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="password" autocomplete="new-password" wire:model="password.new" aria-describedby="new-password-help @error('password.new') new-password-error @enderror" @error('password.new') aria-invalid="true" @enderror>
                        <p id="new-password-help" class="mt-2 text-xs leading-5 text-catalyst-muted">Use at least {{ $state['password_policy']['minimum_length'] }} characters for this prototype.</p>
                        @error('password.new') <p id="new-password-error" class="mt-1 text-sm text-status-error-ink">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium" for="password-confirmation">Confirm New Password</label>
                        <input id="password-confirmation" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="password" autocomplete="new-password" wire:model="password.confirmation" @error('password.confirmation') aria-invalid="true" aria-describedby="password-confirmation-error" @enderror>
                        @error('password.confirmation') <p id="password-confirmation-error" class="mt-2 text-sm text-status-error-ink">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if ($passwordFeedback)
                    <p class="mt-5 text-sm font-medium text-status-success-ink" role="status">{{ $passwordFeedback }}</p>
                @endif

                <button class="mt-6 inline-flex min-h-11 items-center justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="submit">Update Password</button>
            </form>
        </div>
    </x-ui.container>
</div>
