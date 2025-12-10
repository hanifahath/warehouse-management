<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Management System</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .btn-primary {
            background: #4f46e5;
            color: white;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            font-size: 0.9375rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary:hover {
            background: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
        .btn-secondary {
            border: 1px solid #d1d5db;
            color: #374151;
            background: white;
            padding: 0.625rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.2s;
            font-size: 0.9375rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-secondary:hover {
            border-color: #4f46e5;
            color: #4f46e5;
            background: #f8fafc;
        }
        .card-clean {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        .card-clean:hover {
            border-color: #c7d2fe;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            transform: translateY(-4px);
        }
        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        .section-padding {
            padding: 4rem 0;
        }
        .feature-icon {
            width: 3.5rem;
            height: 3.5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Navigation Bar -->
    <nav class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="container-custom">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center shadow">
                        <i class="fas fa-warehouse text-white text-lg"></i>
                    </div>
                    <div>
                        <span class="text-xl font-bold text-gray-900">WMS</span>
                        <span class="text-sm text-gray-500 ml-2 hidden sm:inline">Warehouse Management</span>
                    </div>
                </div>
                
                <div class="hidden md:flex gap-6 text-base">
                    <a href="#features" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors hover:font-semibold">Features</a>
                    <a href="#about" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors hover:font-semibold">About</a>
                    <a href="#contact" class="text-gray-700 hover:text-indigo-600 font-medium transition-colors hover:font-semibold">Contact</a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="btn-secondary">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="btn-primary">
                        <i class="fas fa-user-plus"></i>
                        Supplier
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-white">
        <div class="container-custom section-padding">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">
                        Warehouse Management System
                    </h1>
                    <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                        Sistem manajemen gudang modern untuk mengelola produk, transaksi, dan restock dengan cepat dan akurat.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('login') }}" class="btn-primary text-center justify-center">
                            <i class="fas fa-sign-in-alt"></i>
                            Login to Dashboard
                        </a>
                        <a href="{{ route('register') }}" class="btn-secondary text-center justify-center">
                            <i class="fas fa-user-plus"></i>
                            Register Supplier
                        </a>
                    </div>
                </div>
                
                <div class="lg:w-1/2">
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl border border-indigo-100 p-6 shadow-lg">
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <div class="flex justify-between items-center mb-6 pb-4 border-b border-gray-100">
                                <h3 class="text-gray-900 font-bold text-xl">System Overview</h3>
                                <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">Demo Preview</span>
                            </div>
                            
                            <!-- Stats Grid -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-100 p-2.5 rounded-lg">
                                            <i class="fas fa-box text-blue-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 font-medium">Products</p>
                                            <p class="text-2xl font-bold text-gray-900">30+</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-green-100 p-2.5 rounded-lg">
                                            <i class="fas fa-clipboard-check text-green-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 font-medium">Categories</p>
                                            <p class="text-2xl font-bold text-gray-900">10+</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Features List -->
                            <div class="space-y-3 pt-4 border-t border-gray-100">
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                                    <span class="font-medium">Real-time Inventory Tracking</span>
                                </div>
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                                    <span class="font-medium">Restock Order Management</span>
                                </div>
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                                    <span class="font-medium">Multi-user Access Control</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-gray-50">
        <div class="container-custom section-padding">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Core Features
                </h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">
                    Semua yang Anda butuhkan untuk mengelola gudang dengan efisien
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Feature 1 -->
                <div class="card-clean text-center">
                    <div class="feature-icon bg-indigo-100">
                        <i class="fas fa-boxes text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Inventory Management</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Kelola produk dengan SKU unik, kategori, dan pelacakan stok real-time
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="card-clean text-center">
                    <div class="feature-icon bg-blue-100">
                        <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Restock Orders</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Buat dan kelola pesanan restok dengan status tracking lengkap
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="card-clean text-center">
                    <div class="feature-icon bg-green-100">
                        <i class="fas fa-chart-line text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Reports & Analytics</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Dashboard dengan statistik real-time dan laporan detail
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="card-clean text-center">
                    <div class="feature-icon bg-yellow-100">
                        <i class="fas fa-users-cog text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">User Management</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Multi-user dengan role-based permissions
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="bg-white">
        <div class="container-custom section-padding">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    About Warehouse Management System
                </h2>
                <p class="text-gray-600 text-lg max-w-3xl mx-auto">
                    Sistem yang dikembangkan untuk membantu bisnis mengelola inventori gudang dengan lebih efisien,
                    mengurangi kesalahan manual, dan meningkatkan produktivitas operasional.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="text-center p-6 bg-gray-50 rounded-2xl">
                    <div class="text-4xl font-bold text-indigo-600 mb-4">100%</div>
                    <div class="text-xl font-bold text-gray-900 mb-2">Web-Based</div>
                    <p class="text-gray-600">Akses dari mana saja melalui browser</p>
                </div>
                
                <div class="text-center p-6 bg-gray-50 rounded-2xl">
                    <div class="text-4xl font-bold text-indigo-600 mb-4">24/7</div>
                    <div class="text-xl font-bold text-gray-900 mb-2">Availability</div>
                    <p class="text-gray-600">Sistem tersedia kapan saja</p>
                </div>
                
                <div class="text-center p-6 bg-gray-50 rounded-2xl">
                    <div class="text-4xl font-bold text-indigo-600 mb-4">Multi</div>
                    <div class="text-xl font-bold text-gray-900 mb-2">User Collaboration</div>
                    <p class="text-gray-600">Kerja sama tim dengan role berbeda</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
<section class="bg-gradient-to-r from-indigo-600 to-purple-600 my-12 md:my-16">
    <div class="container-custom py-16 md:py-20">
        <div class="text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Ready to Streamline Your Warehouse?
            </h2>
            <p class="text-indigo-100 text-lg md:text-xl mb-8 max-w-2xl mx-auto">
                Mulai gunakan sistem manajemen gudang yang modern dan efisien
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('login') }}" 
                   class="bg-white text-indigo-600 hover:bg-gray-100 px-8 py-3 rounded-xl font-semibold transition-all inline-flex items-center justify-center gap-2 text-lg shadow-lg hover:shadow-xl">
                    <i class="fas fa-sign-in-alt"></i>
                    Login to System
                </a>
                <a href="{{ route('register') }}" 
                   class="border-2 border-white text-white hover:bg-white hover:text-indigo-600 px-8 py-3 rounded-xl font-semibold transition-all inline-flex items-center justify-center gap-2 text-lg shadow-lg hover:shadow-xl">
                    <i class="fas fa-user-plus"></i>
                    Register as Supplier
                </a>
            </div>
        </div>
    </div>
</section>

<div class="my-12"></div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="container-custom py-12">
            <div class="flex flex-col lg:flex-row justify-between items-start gap-10">
                <div class="lg:w-1/3">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center shadow">
                            <i class="fas fa-warehouse text-white text-xl"></i>
                        </div>
                        <div>
                            <span class="text-2xl font-bold block">WMS</span>
                            <span class="text-gray-400">Warehouse Management System</span>
                        </div>
                    </div>
                    <p class="text-gray-400 text-lg">
                        Sistem manajemen gudang modern untuk bisnis yang efisien dan produktif.
                    </p>
                </div>
                
                <div id="contact" class="lg:w-1/3">
                    <h4 class="text-xl font-bold mb-6">Contact Us</h4>
                    <div class="space-y-4">
                        <p class="flex items-center gap-3 text-gray-300">
                            <i class="fas fa-envelope text-indigo-400 text-lg"></i>
                            support@warehouse-system.com
                        </p>
                        <p class="flex items-center gap-3 text-gray-300">
                            <i class="fas fa-phone text-indigo-400 text-lg"></i>
                            +62 812 3456 7890
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-gray-500 text-center md:text-left">
                        © {{ date('Y') }} Warehouse Management System. All rights reserved.
                    </p>
                    <p class="text-gray-500">
                        Developed with Laravel & Tailwind CSS • v1.0.0
                    </p>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>