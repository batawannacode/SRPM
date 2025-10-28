<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" type="image/png" href="{{ asset('storage/assets/favicon/favicon-96x96.png') }}" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="{{ asset('storage/assets/favicon/favicon.svg') }}" />
<link rel="shortcut icon" href="{{ asset('storage/assets/favicon/favicon.ico') }}" />
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/assets/favicon/apple-touch-icon.png') }}" />
<meta name="apple-mobile-web-app-title" content="SRPM" />
<link rel="manifest" href="{{ asset('storage/assets/favicon/site.webmanifest') }}" />

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
<!-- Styles -->
@livewireStyles

