<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <title>{{ $title ?? 'OpenAgents' }}</title>
    <link rel="stylesheet" href="{{ asset('css/globals.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jbm.css') }}">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}">
    @include('partials.scripts')
    <script>
        (function() {
            var sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            document.documentElement.classList.add('sidebar-init');
            document.documentElement.style.setProperty('--sidebar-width', sidebarCollapsed ? '70px' : '270px');
            document.documentElement.style.setProperty('--sidebar-content-opacity', sidebarCollapsed ? '0' : '1');
            document.documentElement.style.setProperty('--sidebar-content-visibility', sidebarCollapsed ? 'hidden' : 'visible');
        })();
    </script>
</head>

<body hx-boost="true" class="bg-background text-foreground font-mono h-screen overflow-hidden antialiased">
    <div class="flex h-full">
        @auth <x-sidebar /> @endauth
        <div class="flex-1 flex flex-col h-full relative">
            <header class="absolute top-0 left-0 right-0 flex justify-between items-center bg-transparent border-b border-border md:border-none p-0 sm:p-3 z-[9999]">
                <div class="flex flex-row items-center gap-x-4 px-3 py-1">
                    <x-app-brand />
                </div>
                <div class="pr-3">
                    @include('dashboard.top-buttons')
                </div>
            </header>
            <main class="flex-1 overflow-auto h-full">
                {{ $slot }}
            </main>
        </div>
    </div>
    @auth
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/stream.js') }}"></script>
    @endauth
    @stack('scripts')
</body>

</html>