@php
$config = [
    'appName' => config('app.name'),
    'locale' => $locale = app()->getLocale(),
    'locales' => config('app.locales'),
    'githubAuth' => config('services.github.client_id'),
    'recaptchaSiteKey' => config('services.recaptcha.key'),
    'storagePath' => storage_path(),
    'tokenKey' => Cookie::get('tokenKey'),
    'facebookShareUrl' => $currentHost,
    'PUSHER_APP_KEY' => env('PUSHER_APP_KEY'),
    'PUSHER_PORT' => env('PUSHER_PORT'),
    'ENCRYPTED' => env('ENCRYPTED'),
];


@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>{{ config('app.name') }}</title>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-148178114-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-148178114-1');
  </script>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/solid.css" integrity="sha384-+0VIRx+yz1WBcCTXBkVQYIBVNEFH1eP6Zknm16roZCyeNg2maWEpk/l/KsyFKs7G" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/brands.css" integrity="sha384-1KLgFVb/gHrlDGLFPgMbeedi6tQBLcWvyNUN+YKXbD7ZFbjX6BLpMDf0PJ32XJfX" crossorigin="anonymous">
</head>
<body>
  <div id="app"></div>
  <script src="https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit" async defer>

  </script>
  <script src="https://js.stripe.com/v3/"></script>
  @if(\Request::getRequestUri() != "/events/map"  && \Request::getRequestUri() != "/analytics/followers" &&  \Request::getRequestUri() != "/my-feed" &&
  \Request::getRequestUri() != "/analytics/projects" && \Request::getRequestUri() != "/analytics/polls" && \Request::getRequestUri() != "/home"
  && \Request::getRequestUri() != "/")
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvE9xEo7_ctK8R9n8VCuz3ghWsiqRE8o4&libraries=places"></script>
  @endif
  <script>
    window.config = @json($config);
  </script>
  <script src="{{ mix('dist/js/app.js') }}"></script>
</body>
</html>

<style>
  html{
    height: 100%;
  }

  body {
    overflow-x: hidden;
    font-family: EncodeSansLight;
    background-color: #000;
    color: #fff;
    height: 100%;
  }

  #app{
    overflow-x: hidden;
  }

</style>
