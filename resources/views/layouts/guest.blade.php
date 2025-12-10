<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Autentikasi') | WMS App</title>
    
    <!-- 1. Impor Aset menggunakan Vite (Disarankan) -->
    <!-- Pastikan Anda sudah menjalankan 'npm install' dan 'npm run dev' -->
    @vite(['resources/css/app.css', 'resources/js/app.js']) 

    <!-- 2. Font Inter (Diimpor terpisah karena tidak diurus oleh Vite secara default) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f9; /* Latar belakang yang netral */
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">

    <!-- Kontainer Penuh Halaman -->
    <div class="w-full max-w-md p-8 sm:p-10">

        <!-- Logo/Judul Aplikasi -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-indigo-600 tracking-tight">
                WMS App
            </h1>
            <p class="mt-2 text-gray-500 text-sm">Sistem Manajemen Gudang</p>
        </div>

        <!-- Kartu Utama yang menampung form -->
        <div class="bg-white p-6 sm:p-8 rounded-xl shadow-2xl border border-gray-200">
            
            <!-- SLOT UTAMA UNTUK KONTEN KOMPONEN -->
            {{ $slot }}
            
        </div>
        
        <!-- Footer / Credits -->
        <div class="mt-6 text-center text-sm text-gray-500">
            &copy; 2025 Warehouse Management System.
        </div>
        
    </div>

</body>
</html>