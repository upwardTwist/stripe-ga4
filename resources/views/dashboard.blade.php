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
                    Hello {{ auth()->user()->name }}

                    @if(auth()->user()->hasActivePlan())
                    <!-- Content for users with an active plan -->
                    <div>
                        <h3>Welcome to Your Premium Content!</h3>
                        <p>You have successfully subscribed to our premium monthly plan. start using our services</p>
                        <a href="/google/login" class="btn btn-primary">Connect to Google Analytics</a>
                    </div>
                    @else
                    <!-- Message for users without an active plan -->
                    <div>
                        <div class="container">
                            <h2>Subscribe to a Plan</h2>
                            <p>With just $50/month. you can use our services</p>
                            <!-- Display any success or error messages -->
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
                            <!-- Subscription Form -->
                            <form action="{{ route('process.payment') }}" id="subscription-form" method="POST">
                                @csrf
                                <div id="card-element">
                                    <!-- A Stripe Element will be inserted here. -->
                                </div>
                                <button type="submit">Submit Payment</button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="https://js.stripe.com/v3/"></script>

<script>
    // Create a Stripe client.
    var stripe = Stripe('pk_test_51KC9HaFnsuKK3c1Rms5ASrStTlsVMiuf4TKTHTbp5jyMb3THAIy6VuZoiVjgu5NODATqqCNtliDqTKkDITPEUIhb00lRfS4gTn');
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