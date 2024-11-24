<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Panel;

class PanelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $panels = Panel::all()->map(function ($panel) {
            // Konversi model Panel menjadi array
            $panelArray = $panel->toArray();

            // Ubah setiap nilai 0 menjadi '-'
            foreach ($panelArray as $key => $value) {
                if ($value === 0) {
                    $panelArray[$key] = '-';
                }
            }

            return $panelArray;
        });

        return response()->json($panels);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function listNoApp()
    {
        $data = Panel::select('id_panel', 'no_app')->get();
        return response()->json(["datas"=> $data], 200);
    }
}
