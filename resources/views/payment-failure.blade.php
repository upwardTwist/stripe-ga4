<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
</head>

<body>
    <h1>Payment Failed</h1>
    <!-- Display the error message, if available -->
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    <p>There was an issue processing your payment. Please try again later or contact support.</p>
    <!-- You can provide troubleshooting tips or contact information for support here. -->
</body>

</html>