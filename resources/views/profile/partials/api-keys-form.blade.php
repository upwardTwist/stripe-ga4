<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Your Api keys and callback-url information') }}
        </h2>
    </header>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    <div>
        <x-input-label for="Your Call back url" :value="__('Your Webhook-callback url.')" />
        <x-text-input disabled id="GA4 Measurement Protocol Api Secret" name="ga4_measurement_protocol" type="text" class="mt-1 block w-full" :value="old('name', $keys->webhook_url ??'')" required autofocus autocomplete="name" />
    </div>

    <form method="post" action="{{ route('keys.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        <div>
            <x-input-label for="GA4 measurement ID" :value="__('GA4 measurement ID')" />
            <x-text-input disabled id="GA4 measurement ID" name="ga4_measurement_id" type="text" class="mt-1 block w-full" :value="old('name', $keys->ga4_measurement_id ?? '')" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('ga4_measurement_id')" />
        </div>
        <div>
            <x-input-label for="GA4 API Secret" :value="__('GA4 API Secret')" />
            <x-text-input disabled id="GA4 API Secret" name="ga4_api_secret" type="text" class="mt-1 block w-full" :value="old('name', $keys->ga4_api_secret ?? '')" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('ga4_api_secret')" />
        </div>
        <div>
            @if( $keys->ga4_api_secret && isset($keys->ga4_api_secret))
            <x-input-label for="GA4 API Secret" :value="__('Enabled Events')" />
            <ul class="list-inline">
                @foreach($keys->enabled_events as $events)
                <li class="list-inline-item badge badge-success">{{$events}}</li>
                @endforeach
            </ul>
            @endif
        </div>
    </form>

</section>