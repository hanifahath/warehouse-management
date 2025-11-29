<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Warehouse Management')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">

    {{-- Navbar --}}
    @include('partials.navigation')

    <div class="flex">
        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content --}}
        <main class="flex-1 p-6">
            {{-- Flash Messages --}}
            @include('partials.alerts')

            {{-- Page Content --}}
            @yield('content')
        </main>
    </div>

    {{-- Footer --}}
    @include('partials.footer')

</body>
</html>