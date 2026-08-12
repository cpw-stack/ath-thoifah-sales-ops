@extends('layouts.app')

@section('title', 'Edit Area')

@section('content')
<div class="max-w-2xl mx-auto">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Data Area</h2>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <form action="{{ route('admin.areas.update', $area) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-sm text-gray-700">Kode Area</label>
                <input type="text" name="code" value="{{ $area->code }}" class="w-full border rounded p-2 mt-1" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm text-gray-700">Nama Area</label>
                <input type="text" name="name" value="{{ $area->name }}" class="w-full border rounded p-2 mt-1" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm text-gray-700">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full border rounded p-2 mt-1">{{ $area->description }}</textarea>
            </div>
            <div class="flex justify-end">
                <a href="{{ route('admin.areas.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded mr-2">Batal</a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection