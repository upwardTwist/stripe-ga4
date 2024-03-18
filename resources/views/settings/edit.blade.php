<x-app-layout>
    <div class="container">
        <h2>Edit API Keys</h2>
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="stripe_key" class="form-label">Stripe Key</label>
                <input type="text" class="form-control" id="stripe_key" name="stripe_key" value="{{ $userApiKey->stripe_key ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label for="stripe_secret" class="form-label">Stripe Secret</label>
                <input type="text" class="form-control" id="stripe_secret" name="stripe_secret" value="{{ $userApiKey->stripe_secret ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label for="ga4_measurement_id" class="form-label">GA4 Measurement ID</label>
                <input type="text" class="form-control" id="ga4_measurement_id" name="ga4_measurement_id" value="{{ $userApiKey->ga4_measurement_id ?? '' }}" required>
            </div>
            <div class="mb-3">
                <label for="ga4_api_secret" class="form-label">GA4 API Secret</label>
                <input type="text" class="form-control" id="ga4_api_secret" name="ga4_api_secret" value="{{ $userApiKey->ga4_api_secret ?? '' }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</x-app-layout>