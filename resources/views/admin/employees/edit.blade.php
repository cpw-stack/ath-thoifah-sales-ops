@extends('layouts.app')

@section('title', 'Edit Salesman')

@section('content')
<div class="max-w-3xl mx-auto">
    <h2 class="display text-2xl mb-6">Edit Data Salesman</h2>
    
    <div class="card">
        <form action="{{ route('admin.employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-5 border-b" style="border-color:var(--border);">
                <div class="sectiontitle">Informasi Akun (Login)</div>
                <div class="text-xs mb-4" style="color:var(--slate);">Data ini digunakan untuk login ke sistem.</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $employee->user->name) }}" class="w-full" required>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Email</label>
                        <input type="email" name="email" value="{{ old('email', $employee->user->email) }}" class="w-full" required>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Password Baru (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="w-full" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">No. WhatsApp</label>
                        <input type="text" name="whatsapp" value="{{ old('whatsapp', $employee->user->whatsapp) }}" class="w-full">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-xs mb-1" style="color:var(--slate);">Alamat Domisili</label>
                    <textarea name="address" rows="2" class="w-full">{{ old('address', $employee->user->address) }}</textarea>
                </div>
                <div class="mt-4 flex items-center gap-4">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Foto Profil</label>
                        <input type="file" name="photo" accept="image/*" class="text-sm">
                    </div>
                    @if($employee->user->photo)
                        <img src="{{ asset('storage/' . $employee->user->photo) }}" class="w-16 h-16 rounded-full object-cover border" style="border-color:var(--border);">
                    @else
                        <div class="w-16 h-16 rounded-full flex items-center justify-center text-gray-400 border" style="border-color:var(--border);">No Foto</div>
                    @endif
                </div>
            </div>

            <div class="p-5">
                <div class="sectiontitle">Data Kepegawaian</div>
                <div class="text-xs mb-4" style="color:var(--slate);">Detail karyawan untuk operasional lapangan.</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Kode Salesman</label>
                        <input type="text" name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" class="w-full" required>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">No. HP Lapangan</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $employee->phone_number) }}" class="w-full">
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Tanggal Lahir</label>
                        <input type="date" name="birth_date" value="{{ old('birth_date', $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '') }}" class="w-full">
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Jenis Kelamin</label>
                        <select name="gender" class="w-full">
                            <option value="">Pilih...</option>
                            <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">No. KTP</label>
                        <input type="text" name="id_card_number" value="{{ old('id_card_number', $employee->id_card_number) }}" class="w-full">
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Area Penjualan</label>
                        <select name="sales_area_id" class="w-full">
                            <option value="">Pilih Area</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ old('sales_area_id', $employee->sales_area_id) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Supervisor</label>
                        <select name="supervisor_id" class="w-full">
                            <option value="">Tidak Ada</option>
                            @foreach($supervisors as $spv)
                                <option value="{{ $spv->id }}" {{ old('supervisor_id', $employee->supervisor_id) == $spv->id ? 'selected' : '' }}>{{ $spv->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs mb-1" style="color:var(--slate);">Status</label>
                        <select name="status" class="w-full">
                            <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end mt-4">
                    <a href="{{ route('admin.employees.index') }}" class="btn-outline mr-2">Batal</a>
                    <button type="submit" class="btn">Update Data Salesman</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection