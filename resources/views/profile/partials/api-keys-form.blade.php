<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Api keys information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your Api keys.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    @if(auth()->user()->hasActivePlan())

    <form method="post" action="{{ route('keys.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- <div>
            <x-input-label for="Stripe key" :value="__('Stripe key')" />
            <x-text-input id="Stripe key" name="stripe_key" type="text" class="mt-1 block w-full" :value="old('name', $keys->stripe_key)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('stripe_key')" />
        </div>

        <div>
            <x-input-label for="Stripe Secret" :value="__('Stripe Secret')" />
            <x-text-input id="Stripe Secret" name="stripe_secret" type="text" class="mt-1 block w-full" :value="old('name', $keys->stripe_secret)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('stripe_secret')" />
        </div> -->

        <div>
            <x-input-label for="Stripe Webhook Secret" :value="__('Stripe Webhook Secret')" />
            <x-text-input id="Stripe Webhook Secret" name="stripe_webhook_secret" type="text" class="mt-1 block w-full" :value="old('name', $keys->stripe_webhook_secret)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('stripe_webhook_secret')" />
        </div>
        <div>
            <x-input-label for="GA4 measurement ID" :value="__('GA4 measurement ID')" />
            <x-text-input id="GA4 measurement ID" name="ga4_measurement_id" type="text" class="mt-1 block w-full" :value="old('name', $keys->ga4_measurement_id)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('ga4_measurement_id')" />
        </div>
        <div>
            <x-input-label for="GA4 API Secret" :value="__('GA4 API Secret')" />
            <x-text-input id="GA4 API Secret" name="ga4_api_secret" type="text" class="mt-1 block w-full" :value="old('name', $keys->ga4_api_secret)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('ga4_api_secret')" />
        </div>
        <div>
            <x-input-label for="GA4 Measurement Protocol Api Secret" :value="__('GA4 Measurement Protocol Api Secret')" />
            <x-text-input id="GA4 Measurement Protocol Api Secret" name="ga4_measurement_protocol" type="text" class="mt-1 block w-full" :value="old('name', $keys->ga4_measurement_protocol)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('ga4_measurement_protocol')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>

        <div>
            <x-input-label for="Your Call back url will appear here" :value="__('Your Call back url will appear here')" />
            <x-text-input disabled id="GA4 Measurement Protocol Api Secret" name="ga4_measurement_protocol" type="text" class="mt-1 block w-full" :value="old('name', $keys->webhook_url)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('ga4_measurement_protocol')" />
        </div>
    </form>

    @else
    <div class="text-white">
        <h3 >Please buy and active plan!</h3>
        <p>This is the content only available for users with an active plan.</p>
    </div>
    @endif
</section>