@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="bg-white border border-gray-200 shadow rounded-lg p-6 text-center">
        <div class="mb-4">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-100">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        
        <h2 class="text-xl font-bold text-gray-900 mb-2">Account Pending Approval</h2>
        
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm">
            <p class="text-yellow-800">
                @auth
                    Your supplier account <strong>{{ auth()->user()->name }}</strong> is waiting for admin approval.
                @else
                    Your supplier account is waiting for admin approval.
                @endauth
            </p>
        </div>
        
        <p class="text-gray-600 mb-6">
            Please wait while our admin team reviews your registration. 
            You will be able to access the system once approved.
        </p>
        
        <div class="flex justify-center space-x-3">
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300">
                        Logout
                    </button>
                </form>
            @endauth
            
            <a href="{{ route('welcome') }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded text-sm hover:bg-indigo-700">
                Back to Home
            </a>
        </div>
    </div>
</div>
@endsection