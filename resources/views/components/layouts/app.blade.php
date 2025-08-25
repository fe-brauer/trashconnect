<!doctype html>
<html lang="de" class="h-full bg-tv-bg [color-scheme:light]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow">

    @stack('head')

    @vite(['resources/css/app.css','resources/js/app.js'])
    @stack('schema') {{-- JSON-LD etc. --}}
    @livewireStyles

    <script>
        // Matomo cookielos + Livewire SPA Pageviews
        var _paq = window._paq = window._paq || [];

        // 🔒 Cookielos & Privacy
        _paq.push(['disableCookies']);          // keine Cookies/LocalStorage
        _paq.push(['setDoNotTrack', true]);     // respektiere DNT
        // KEIN setUserId, KEIN userFingerprint etc.

        // Standard-Pageview + Link-Tracking
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);

        (function() {
            var u = "{{ rtrim(config('services.matomo.url'), '/') }}/";
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '{{ config('services.matomo.site_id') }}' ]);
            var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
            g.async = true; g.src = u + 'matomo.js'; s.parentNode.insertBefore(g, s);
        })();

        // Livewire v3: SPA-Navigation tracken
        document.addEventListener('livewire:navigated', function () {
            if (!window._paq) return;
            _paq.push(['setCustomUrl', location.href]);
            _paq.push(['setDocumentTitle', document.title]);
            _paq.push(['trackPageView']);
        });
    </script>

</head>
<body class="min-h-dvh text-slate-900">
    @include('partials.nav')
<div class="tc-container">

    <main class="py-10">
        {{ $slot }}
    </main>

    @include('partials.footer')
</div>

@livewireScripts
</body>
