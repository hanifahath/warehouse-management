<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Warehouse Management System</title>
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
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12">

    <div class="w-full max-w-md mx-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-14 h-14 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-warehouse text-white text-xl"></i>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                WMS App
            </h1>
            <p class="text-gray-700 text-sm">
                Sistem Manajemen Gudang
            </p>
        </div>

        <!-- Register Card -->
        <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-sm">
            
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

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name Field -->
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-900 mb-2">
                        Name
                    </label>
                    <input 
                        id="name" 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus 
                        autocomplete="name"
                        class="input-clean @error('name') error @enderror"
                        placeholder="Enter your full name"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-red-800 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1.5"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-2">
                        Email
                    </label>
                    <input 
                        id="email" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="username"
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
                        autocomplete="new-password"
                        class="input-clean @error('password') error @enderror"
                        placeholder="Create a password"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-800 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1.5"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-2">
                        Confirm Password
                    </label>
                    <input 
                        id="password_confirmation" 
                        type="password" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password"
                        class="input-clean @error('password_confirmation') error @enderror"
                        placeholder="Confirm your password"
                    >
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-800 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1.5"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Already registered link -->
                <div class="mb-6">
                    <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium transition-colors">
                        Already registered?
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary">
                    <i class="fas fa-user-plus mr-2"></i>REGISTER
                </button>

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
                © {{ date('Y') }} Warehouse Management System. All rights reserved.
            </p>
        </div>

    </div>

</body>
</html>