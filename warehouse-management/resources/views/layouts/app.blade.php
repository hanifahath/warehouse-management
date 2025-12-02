<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Warehouse System' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-700">

    {{-- Top Bar --}}
    <header class="bg-white border-b border-gray-200 h-14 flex items-center px-4 justify-between">
        <div class="text-gray-900 font-bold text-lg">
            Warehouse Management
        </div>

        <div class="flex items-center gap-4">
            <span class="text-gray-600 text-sm">{{ auth()->user()->name ?? 'Guest' }}</span>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="text-sm text-red-600 hover:underline">
                    Logout
                </button>
            </form>
        </div>
    </header>

    <div class="flex">

        {{-- Sidebar --}}
        @include('layouts.sidebar')

        {{-- Content --}}
        <main class="flex-1 p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">{{ $page ?? '' }}</h1>
            @yield('content')
        </main>
    </div>

</body>
</html>
