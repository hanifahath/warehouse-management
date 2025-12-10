@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Category</h1>
            <p class="text-gray-600 mt-1">Update category details and image</p>
        </div>
        
        <a href="{{ route('categories.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Categories
        </a>
    </div>

    {{-- FORM CARD --}}
    <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
        
        {{-- CARD HEADER --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Category Information</h2>
            <div class="flex items-center mt-1">
                <span class="text-sm text-gray-600">Editing: </span>
                <span class="ml-2 px-2 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded">
                    {{ $category->name }}
                </span>
                <span class="ml-3 text-sm text-gray-500">
                    ID: {{ $category->id }}
                </span>
            </div>
        </div>

        {{-- FORM CONTENT --}}
        <form action="{{ route('categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- LEFT COLUMN: Basic Info --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- NAME --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   required
                                   value="{{ old('name', $category->name) }}"
                                   placeholder="e.g., Electronics, Clothing, Food"
                                   class="pl-10 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror" />
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- DESCRIPTION --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  placeholder="Describe this category (optional)"
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-300 @enderror">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- CATEGORY INFO --}}
                    <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Category Details</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Created:</span>
                                <span class="ml-2 text-gray-900">{{ $category->created_at->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Last Updated:</span>
                                <span class="ml-2 text-gray-900">{{ $category->updated_at->format('M d, Y') }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-gray-500">Products in Category:</span>
                                <span class="ml-2 text-gray-900 font-medium">{{ $category->products_count ?? $category->products()->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Image Upload --}}
                <div>
                    {{-- CURRENT IMAGE --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Current Image
                        </label>
                        
                        @if($category->image_path)
                            <div class="relative w-full h-48 rounded-lg overflow-hidden border border-gray-300 mb-3">
                                <img id="current-image" 
                                     src="{{ Storage::url($category->image_path) }}" 
                                     alt="{{ $category->name }}"
                                     class="w-full h-full object-cover">
                                <a href="{{ Storage::url($category->image_path) }}" 
                                   target="_blank"
                                   class="absolute top-2 right-2 bg-blue-500 text-white rounded-full p-1 hover:bg-blue-600 focus:outline-none"
                                   title="View full size">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                    </svg>
                                </a>
                            </div>
                            <div class="text-center">
                                <button type="button" 
                                        id="remove-existing-image"
                                        class="text-sm text-red-600 hover:text-red-800 focus:outline-none">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Remove Current Image
                                </button>
                                <input type="hidden" id="remove_image" name="remove_image" value="0">
                            </div>
                        @else
                            <div class="w-full h-48 rounded-lg border-2 border-dashed border-gray-300 flex flex-col items-center justify-center bg-gray-50">
                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No image uploaded</p>
                            </div>
                        @endif
                    </div>

                    {{-- NEW IMAGE UPLOAD --}}
                    <div>
                        <label for="image_path" class="block text-sm font-medium text-gray-700 mb-3">
                            Upload New Image
                            <span class="text-gray-500 font-normal">(Optional)</span>
                        </label>
                        
                        {{-- New Image Preview --}}
                        <div id="new-image-preview" class="mb-4 hidden">
                            <div class="relative w-full h-48 rounded-lg overflow-hidden border border-gray-300">
                                <img id="preview-new-image" 
                                     src="" 
                                     alt="New image preview"
                                     class="w-full h-full object-cover">
                                <button type="button" 
                                        id="remove-new-image"
                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-2 text-sm text-gray-500 text-center">This will replace the current image</p>
                        </div>

                        {{-- Upload Area --}}
                        <div id="upload-area" 
                             class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition-colors duration-200">
                            <div class="space-y-3">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                                </svg>
                                <div class="flex flex-col items-center">
                                    <label for="image_path" class="cursor-pointer">
                                        <span class="bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            Click to upload
                                        </span>
                                        <span class="ml-1 text-sm text-gray-600">or drag and drop</span>
                                    </label>
                                    <input id="image_path" 
                                           name="image_path" 
                                           type="file" 
                                           accept="image/*" 
                                           class="sr-only" />
                                    <p class="text-xs text-gray-500 mt-2">
                                        PNG, JPG, GIF, SVG up to 2MB
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @error('image_path')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">Recommended: 400×400 pixels, square format.</p>
                    </div>

                    {{-- DANGER ZONE --}}
                    <div class="mt-8 pt-6 border-t border-red-200">
                        <h3 class="text-sm font-medium text-red-800 mb-3">Danger Zone</h3>
                        <div class="text-sm">
                            <button type="button"
                                    onclick="if(confirm('Are you sure you want to delete this category? This action cannot be undone and will delete all associated products!')) { document.getElementById('delete-form').submit(); }"
                                    class="text-red-600 hover:text-red-800 focus:outline-none">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete this category
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM VALIDATION SUMMARY --}}
            @if($errors->any())
            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            There were {{ $errors->count() }} {{ Str::plural('error', $errors->count()) }} with your submission
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- ACTION BUTTONS --}}
            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between items-center">
                <div>
                    <a href="{{ route('categories.show', $category) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View Details
                    </a>
                </div>
                
                <div class="flex space-x-3">
                    <a href="{{ route('categories.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancel
                    </a>
                    
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Category
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- DELETE FORM --}}
<form id="delete-form" action="{{ route('categories.destroy', $category) }}" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image_path');
    const newImagePreview = document.getElementById('new-image-preview');
    const previewNewImage = document.getElementById('preview-new-image');
    const uploadArea = document.getElementById('upload-area');
    const removeNewImageBtn = document.getElementById('remove-new-image');
    const removeExistingImageBtn = document.getElementById('remove-existing-image');
    const removeImageInput = document.getElementById('remove_image');
    
    // Handle new file selection
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewNewImage.src = e.target.result;
                    newImagePreview.classList.remove('hidden');
                    uploadArea.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Handle drag and drop for new image
    if (uploadArea) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-indigo-500', 'bg-indigo-50');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-500', 'bg-indigo-50');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-indigo-500', 'bg-indigo-50');
            
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                imageInput.files = e.dataTransfer.files;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewNewImage.src = e.target.result;
                    newImagePreview.classList.remove('hidden');
                    uploadArea.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Handle new image removal
    if (removeNewImageBtn) {
        removeNewImageBtn.addEventListener('click', function() {
            imageInput.value = '';
            previewNewImage.src = '';
            newImagePreview.classList.add('hidden');
            uploadArea.classList.remove('hidden');
        });
    }
    
    // Handle existing image removal
    if (removeExistingImageBtn && removeImageInput) {
        removeExistingImageBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to remove the current image?')) {
                const currentImage = document.getElementById('current-image');
                if (currentImage) {
                    currentImage.style.opacity = '0.3';
                    currentImage.style.filter = 'grayscale(100%)';
                }
                removeImageInput.value = '1';
                removeExistingImageBtn.classList.add('line-through');
                removeExistingImageBtn.innerHTML = '<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Image will be removed';
            }
        });
    }
    
    // Auto-focus name field
    document.getElementById('name').focus();
});
</script>
@endpush