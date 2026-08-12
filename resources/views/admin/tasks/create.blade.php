@extends('layouts.app')

@section('title', 'Tambah Task')

@section('content')
<div class="max-w-3xl mx-auto">
    <h2 class="display text-2xl mb-6">Tambah Tugas Baru</h2>
    
    <div class="card">
        <form action="{{ route('admin.tasks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="p-5 space-y-4">
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Judul Tugas</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full" required>
                    @error('title') <div class="text-xs mt-1" style="color:var(--red);">{{ $message }}</div> @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Salesman</label>
                        <select name="employee_id" class="w-full" required>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Toko Mitra (Opsional)</label>
                        <select name="customer_id" class="w-full">
                            <option value="">Tidak ada toko spesifik</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>{{ $cust->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Deskripsi Tugas</label>
                    <textarea name="description" rows="3" class="w-full">{{ old('description') }}</textarea>
                </div>

                <!-- Bagian Lampiran -->
                <div>
                    <label class="block text-xs mb-1" style="color:var(--slate);">Lampiran Invoice (PDF/JPG)</label>
                    <input type="file" name="attachment" class="text-sm">
                    @error('attachment') <div class="text-xs mt-1" style="color:var(--red);">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Prioritas</label>
                        <select name="priority" class="w-full">
                            <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Deadline</label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" class="w-full" required>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Status</label>
                        <select name="status" class="w-full">
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="overdue" {{ old('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end pt-4">
                    <a href="{{ route('admin.tasks.index') }}" class="btn-outline mr-2">Batal</a>
                    <button type="submit" class="btn">Simpan Tugas</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection