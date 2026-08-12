@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="max-w-3xl mx-auto">
    <h2 class="display text-2xl mb-6">Edit Data Tugas</h2>
    
    <div class="card">
        <form action="{{ route('admin.tasks.update', $task->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Judul Tugas</label>
                    <input type="text" name="title" value="{{ old('title', $task->title) }}" class="w-full" required>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Salesman</label>
                        <select name="employee_id" class="w-full" required>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id', $task->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Toko Mitra (Opsional)</label>
                        <select name="customer_id" class="w-full">
                            <option value="">Tidak ada toko spesifik</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}" {{ old('customer_id', $task->customer_id) == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Deskripsi Tugas</label>
                    <textarea name="description" rows="3" class="w-full">{{ old('description', $task->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Lampiran (Opsional)</label>
                    <input type="file" name="attachment" class="w-full">
                    @if($task->attachment)
                        <p class="text-xs mt-1">Lampiran saat ini: <a href="{{ asset('storage/' . $task->attachment) }}" target="_blank" class="text-blue-600 underline">Lihat Lampiran</a></p>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Prioritas</label>
                        <select name="priority" class="w-full">
                            <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Deadline</label>
                        <input type="date" name="due_date" value="{{ old('due_date', $task->due_date->format('Y-m-d')) }}" class="w-full" required>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Status</label>
                        <select name="status" class="w-full">
                            <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="overdue" {{ old('status', $task->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4">
                    <a href="{{ route('admin.tasks.index') }}" class="btn-outline mr-2">Batal</a>
                    <button type="submit" class="btn">Update Tugas</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection