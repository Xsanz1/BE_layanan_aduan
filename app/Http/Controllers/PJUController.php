<?php

namespace App\Http\Controllers;

use App\Models\PJU;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PJUImport;

class PJUController extends Controller
{
    // Get all data
    public function index()
    {
        $pjus = PJU::all();
        return response()->json($pjus);
    }

    // Get specific data by ID
    public function show($id)
    {
        $pjus = PJU::find($id);
        if (!$pjus) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        return response()->json($pjus, 201);
    }

    // Create new data
    public function store(Request $request)
    {
        $pjus = PJU::create($request->all());
        return response()->json($pjus, 201);
    }

    // Update existing data
    public function update(Request $request, $id)
    {
        $pjus = PJU::find($id);
        if (!$pjus) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        $pjus->update($request->all());
        return response()->json($pjus, 201);
    }

    // Delete data
    public function destroy($id)
    {
        $pjus = PJU::find($id);
        if (!$pjus) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        $pjus->delete();
        return response()->json(['message' => 'Data deleted successfully'], 201);
    }
    public function listNoTiangBaru()
    {
        $data = PJU::select('id_pju', 'No_Tiang_baru')->get();
        return response()->json(["datas"=> $data], 201);
    }

    public function importPju(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new PjuImport, $request->file('file'));

        return response()->json(['sukses menambahkan data'], 201);
    }
}

