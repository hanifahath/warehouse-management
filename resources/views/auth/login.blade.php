<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Warehouse Management System</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .input-clean {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            width: 100%;
            transition: all 0.2s;
            color: #111827;
            background: white;
        }
        .input-clean:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .input-clean.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        .btn-primary {
            background: #4f46e5;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: background 0.2s;
            width: 100%;
            border: none;
        }
        .btn-primary:hover {
            background: #4338ca;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md mx-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-warehouse text-white text-xl"></i>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                Warehouse Management System
            </h1>
            <p class="text-gray-700 text-sm">
                Please sign in to your account
            </p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-sm">
            
            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 text-green-800 rounded-lg text-sm border border-green-200 flex items-start">
                    <i class="fas fa-check-circle mt-0.5 mr-3"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-lg text-sm border border-red-200">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle mt-0.5 mr-3"></i>
                        <div class="flex-1">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Field -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-2">
                        Email Address
                    </label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus 
                        autocomplete="email"
                        class="input-clean @error('email') error @enderror"
                        placeholder="name@company.com"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-800 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1.5"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-900 mb-2">
                        Password
                    </label>
                    <input 
                        id="password" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        class="input-clean @error('password') error @enderror"
                        placeholder="Enter your password"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-800 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1.5"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-6">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember"
                        class="h-4 w-4 text-indigo-600 border-gray-200 rounded focus:ring-indigo-600"
                    >
                    <label for="remember_me" class="ml-2 text-sm text-gray-700 font-medium">
                        Keep me signed in
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary mb-6">
                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                </button>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-white text-gray-500 font-medium">Demo Accounts</span>
                    </div>
                </div>

                <!-- Demo Accounts Info -->
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">Available User Roles:</h3>
                    <div class="space-y-2.5">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 font-medium">Admin:</span>
                            <code class="bg-white border border-gray-200 px-3 py-1 rounded text-xs text-gray-900">admin@inventory.test</code>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 font-medium">Manager:</span>
                            <code class="bg-white border border-gray-200 px-3 py-1 rounded text-xs text-gray-900">manager@inventory.test</code>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 font-medium">Staff:</span>
                            <code class="bg-white border border-gray-200 px-3 py-1 rounded text-xs text-gray-900">staff1@inventory.test</code>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 font-medium">Supplier:</span>
                            <code class="bg-white border border-gray-200 px-3 py-1 rounded text-xs text-gray-900">supplier1@inventory.test</code>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <p class="text-xs text-gray-700 flex items-center justify-center">
                            <i class="fas fa-key mr-2 text-gray-500"></i>
                            All passwords: <code class="bg-white border border-gray-200 px-2 py-0.5 rounded ml-2 text-gray-900">password123</code>
                        </p>
                    </div>
                </div>

            </form>

            <!-- Back to Home -->
            <div class="text-center mt-6 pt-6 border-t border-gray-200">
                <a href="{{ url('/') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Home
                </a>
            </div>

        </div>

        <!-- Footer Note -->
        <div class="text-center mt-8">
            <p class="text-xs text-gray-500">
                For account issues or password reset, please contact your system administrator.
            </p>
            <p class="text-xs text-gray-500 mt-2">
                © {{ date('Y') }} Warehouse Management System. All rights reserved.
            </p>
        </div>

    </div>

</body>
</html>