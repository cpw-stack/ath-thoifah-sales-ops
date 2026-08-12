<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesArea;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AreaImport;
use App\Exports\AreaTemplateExport;

class SalesAreaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $areas = SalesArea::where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%")
            ->latest()
            ->paginate(10);

        return view('admin.areas.index', compact('areas'));
    }

    public function create()
    {
        return view('admin.areas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:sales_areas,code',
            'description' => 'nullable|string',
        ]);

        SalesArea::create($validated);
        return redirect()->route('admin.areas.index')->with('success', 'Area penjualan berhasil ditambahkan.');
    }

    public function edit(SalesArea $area)
    {
        return view('admin.areas.edit', compact('area'));
    }

    public function update(Request $request, SalesArea $area)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|unique:sales_areas,code,' . $area->id,
            'description' => 'nullable|string',
        ]);

        $area->update($validated);
        return redirect()->route('admin.areas.index')->with('success', 'Area penjualan berhasil diperbarui.');
    }

    public function destroy(SalesArea $area)
    {
        $area->delete();
        return redirect()->route('admin.areas.index')->with('success', 'Area penjualan berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        Excel::import(new AreaImport, $request->file('file'));
        return back()->with('success', 'Data area berhasil diimpor!');
    }

    public function template()
    {
        return Excel::download(new AreaTemplateExport, 'Template_Import_Area.xlsx');
    }
}