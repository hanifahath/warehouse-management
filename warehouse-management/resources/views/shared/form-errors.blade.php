@if ($errors->any())
    <div class="bg-red-500 text-white p-3 rounded mb-4">
        <strong>There were some problems with your input:</strong>
        <ul class="list-disc pl-5 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif