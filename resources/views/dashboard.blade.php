<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-white-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-black-900 dark:text-black-100">
                    @if(session('success'))
                    <div class="alert alert-success">
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-danger">
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    </div>
                    @endif
                    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl">
                        <div class="md:flex">
                            <div class="p-8">
                                <div class="card">
                                    <div class="card-header">
                                        Step 1: Stripe connectivity
                                    </div>
                                    <div class="card-body">
                                        @if(auth()->user()->hasEnabledWebhookUrl())

                                        @if(session('success'))
                                        <div class="alert alert-success">
                                            <div class="alert alert-success" role="alert">
                                                This is a success alert—check it out!
                                            </div>
                                        </div>
                                        @endif
                                        <h5 class="card-title"> Stripe Status</h5>
                                        <button type="button" disabled class="btn btn-success"> Connected</button>
                                        @else
                                        <h5 class="card-title">Connect your Stripe</h5>
                                        <p class="card-text">Lets get you started by setting up your webhook-url for Stripe</p>
                                        <a href="/stripe/connect" class="btn btn-primary">Connect Stripe</a>
                                        @endif
                                    </div>
                                </div>
                                <p class="mt-2 text-gray-500">.</p>
                            </div>
                        </div>
                    </div>
                    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl">
                        <div class="md:flex">
                            <div class="p-8">
                                <div class="card">
                                    <div class="card-header">
                                        Step 2: Google Connectivity
                                    </div>
                                    <div class="card-body">
                                        @if(auth()->user()->hasEnabledG4Keys())
                                        <h5 class="card-title"> Google Status</h5>
                                        <button type="button" disabled class="btn btn-success"> Connected</button>
                                        @else

                                        @if(session('google_message'))
                                        <div class="alert alert-danger">
                                            "We detected that you do not have a Google Analytics account. To proceed, please create a Google Analytics account. [Follow these instructions] <a href="https://analytics.google.com/analytics/web/#/provision/SignUp" target="_blank">(https://analytics.google.com/analytics/web/#/provision/SignUp)</a> to create your account. After creating your account, please return here to continue the setup process."
                                        </div>
                                        @endif
                                        @if( session('accounts'))
                                        <div class="alert alert-info" role="alert">
                                            Please Select a Ga4 Account
                                        </div>
                                        <form action="{{ route('property.create') }}" method="POST">
                                            @csrf
                                            @foreach(session('accounts') as $account)
                                            <div>
                                                <input type="checkbox" name="accounts[]" value="{{ $account['name'] }}" id="account_{{ $account['name'] }}">
                                                <label for="account_{{ $account['name'] }}">
                                                    {{ $account['displayName'] }} ({{ $account['name'] }})
                                                </label>
                                            </div>
                                            @endforeach
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                        @endif

                                        @if(!session('accounts'))
                                        <h5 class="card-title">Connect your google</h5>
                                        <p class="card-text">Lets Connect your google analytics account to create your measurement ids and secrets.</p>
                                        <a href="/google/login" class="btn btn-primary">Connect google </a>
                                        @endif
                                        @endif
                                    </div>
                                </div>
                                <p class="mt-2 text-gray-500">.</p>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="container">
                        <h2>Subscribe to a Plan</h2>
                        <p>With just $50/month. you can use our services</p>
                        @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif
                        @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                        @endif
                        <form action="{{ route('process.payment') }}" id="subscription-form" method="POST">
                            @csrf
                            <div id="card-element">
                            </div>
                            <button type="submit">Submit Payment</button>
                        </form>
                    </div> -->
                </div>

            </div>
        </div>
    </div>
    </div>
</x-app-layout>
<script src="https://js.stripe.com/v3/"></script>

<script>
    var stripeKey = "{{ config('services.stripe.key') }}";
    // Create a Stripe client.
    var stripe = Stripe(stripeKey);
    // Create an instance of Elements.
    var elements = stripe.elements();
    // Create an instance of the card Element.
    var card = elements.create('card');
    // Add an instance of the card Element into the `card-element` div.
    card.mount('#card-element');

    var form = document.getElementById('subscription-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        stripe.createToken(card).then(function(result) {
            if (result.error) {
                // Inform the user if there was an error.
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                // Send the token to your server.
                stripeTokenHandler(result.token);
            }
        });
    });

    function stripeTokenHandler(token) {
        // Insert the token ID into the form so it gets submitted to the server
        var form = document.getElementById('subscription-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);

        // Submit the form
        form.submit();
    }
</script>