<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
</head>

<body>
    <h1>Payment Successful</h1>

    <!-- Display the success message, if available -->
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <p>Your payment has been processed successfully.</p>
    <!-- You can customize this view further with additional information or a thank you message. -->
</body>

</html