<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body onload="document.forms[0].submit()">
    <form method="POST" action="{{ $baseUri }}" accept-charset="utf-8">
        <input type="hidden" name="SAMLRequest" value="{{ $samlParameters['SAMLRequest'] }}"/>
        <input type="hidden" name="RelayState" value="{{ $samlParameters['RelayState'] }}"/>
        <noscript>
            <p>
                <strong>Note:</strong> Since your browser does not support JavaScript,
                you must press the Continue button once to proceed.
            </p>
            <div>
                <input type="submit" value="Continue"/>
            </div>
        </noscript>
    </form>
</body>
</html>
