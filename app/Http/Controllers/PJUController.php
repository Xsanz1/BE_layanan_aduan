<?php

namespace App\Http\Controllers;

use App\Models\PJU;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PJUImport;
use Illuminate\Support\Facades\Log;

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
        return response()->json($pjus, 200);
    }

    // Create new data
    public function store(Request $request)
    {
        $pjus = PJU::create($request->all());
        return response()->json($pjus, 200);
    }

    // Update existing data
    public function update(Request $request, $id)
    {
        $pjus = PJU::find($id);
        if (!$pjus) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        $pjus->update($request->all());
        return response()->json($pjus, 200);
    }

    // Delete data
    public function destroy($id)
    {
        $pjus = PJU::find($id);
        if (!$pjus) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        $pjus->delete();
        return response()->json(['message' => 'Data deleted successfully'], 200);
    }
    public function listNoTiangBaru()
    {
        $data = PJU::select('id_pju', 'no_tiang_baru')->get();
        return response()->json(["datas" => $data], 200);
    }
    
    public function getPjuByPanel($panelId)
    {
        Log::info("Fetching PJU for panel ID: {$panelId}");
        $data = Pju::where('panel_id', $panelId)->get();

        if ($data->isEmpty()) {
            Log::warning("No PJU data found for panel ID: {$panelId}");
        }

        return response()->json(['datas' => $data], 200);
    }
    // Count total PJU data
    public function countPJU()
    {
        $count = PJU::count();
        return response()->json(['total_pju' => $count], 200);
    }
    
}
