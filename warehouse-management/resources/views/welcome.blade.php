<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse System</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 text-gray-700">

    <div class="min-h-screen flex flex-col justify-center items-center px-6">

        {{-- Heading --}}
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            Warehouse Management System
        </h1>

        {{-- Subtext --}}
        <p class="text-gray-600 max-w-xl text-center mb-10">
            Sistem manajemen gudang modern untuk mengelola produk, transaksi, dan restock dengan cepat dan akurat.
        </p>

        {{-- Buttons --}}
        <div class="flex gap-4">

            <a href="{{ route('login') }}"
               class="bg-indigo-600 hover:bg-indigo-700 px-6 py-3 rounded shadow">
                Login
            </a>

            <a href="{{ route('register') }}"
               class="border border-gray-300 hover:bg-gray-100 px-6 py-3 rounded">
                Register Supplier
            </a>

        </div>

        {{-- Footer --}}
        <p class="mt-10 text-sm text-gray-500">
            © {{ date('Y') }} Warehouse System. All rights reserved.
        </p>

    </div>

</body>
</html>
