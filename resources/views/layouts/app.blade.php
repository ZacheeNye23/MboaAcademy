<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MboaAcademy — Apprends. Grandis. Rayonne.')</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">

    {{-- Vite : Tailwind CSS + JS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Styles spécifiques à la page --}}
    @stack('styles')
</head>

<body class="font-outfit bg-cream text-dark antialiased overflow-x-hidden">

    {{-- Navbar (cachée sur login/register ou si forcé) --}}
    @if (
        !in_array(Route::currentRouteName(), ['login', 'register']) 
        && !isset($hideNavbar)
    )
        @include('components.navbar')
    @endif


    {{-- Contenu principal --}}
    <main>
        @yield('content')
    </main>


    {{-- Footer (caché sur login/register ou si forcé) --}}
    @if (
        !in_array(Route::currentRouteName(), ['login', 'register']) 
        && !isset($hideFooter)
    )
        @include('components.footer')
    @endif


    {{-- Scripts spécifiques à la page --}}
    @stack('scripts')

</body>
</html>