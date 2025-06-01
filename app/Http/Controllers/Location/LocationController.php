<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Dusun;
use App\Models\RW;
use App\Models\RT;

class LocationController extends Controller
{
    // Constructor for authorization access
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    // CRUD for Kecamatan
    public function indexKecamatan()
    {
        return response()->json(Kecamatan::all());
    }

    public function storeKecamatan(Request $request)
    {
        $request->validate([
            'Nama_Kecamatan' => 'required|string|max:255'
        ]);

        $kecamatan = Kecamatan::create($request->all());
        return response()->json($kecamatan, 201);
    }

    public function showKecamatan($id)
    {
        $kecamatan = Kecamatan::findOrFail($id);
        return response()->json($kecamatan);
    }

    public function updateKecamatan(Request $request, $id)
    {
        $request->validate([
            'Nama_Kecamatan' => 'required|string|max:255'
        ]);

        $kecamatan = Kecamatan::findOrFail($id);
        $kecamatan->update($request->all());
        return response()->json($kecamatan);
    }

    public function destroyKecamatan($id)
    {
        $kecamatan = Kecamatan::findOrFail($id);
        $kecamatan->delete();
        return response()->json(null, 204);
    }

    // CRUD for Desa
    public function indexDesa()
    {
        return response()->json(Desa::all());
    }

    public function storeDesa(Request $request)
    {
        $request->validate([
            'Nama_Desa' => 'required|string|max:255',
            'kecamatan_id' => 'required|exists:kecamatan,id'
        ]);

        $desa = Desa::create($request->all());
        return response()->json($desa, 201);
    }

    public function showDesa($id)
    {
        $desa = Desa::findOrFail($id);
        return response()->json($desa);
    }

    public function updateDesa(Request $request, $id)
    {
        $request->validate([
            'Nama_Desa' => 'required|string|max:255',
            'kecamatan_id' => 'required|exists:kecamatan,id'
        ]);

        $desa = Desa::findOrFail($id);
        $desa->update($request->all());
        return response()->json($desa);
    }

    public function destroyDesa($id)
    {
        $desa = Desa::findOrFail($id);
        $desa->delete();
        return response()->json(null, 204);
    }

    // CRUD for Dusun
    public function indexDusun()
    {
        return response()->json(Dusun::all());
    }

    public function storeDusun(Request $request)
    {
        $request->validate([
            'Nama_Dusun' => 'required|string|max:255',
            'desa_id' => 'required|exists:desa,id'
        ]);

        $dusun = Dusun::create($request->all());
        return response()->json($dusun, 201);
    }

    public function showDusun($id)
    {
        $dusun = Dusun::findOrFail($id);
        return response()->json($dusun);
    }

    public function updateDusun(Request $request, $id)
    {
        $request->validate([
            'Nama_Dusun' => 'required|string|max:255',
            'desa_id' => 'required|exists:desa,id'
        ]);

        $dusun = Dusun::findOrFail($id);
        $dusun->update($request->all());
        return response()->json($dusun);
    }

    public function destroyDusun($id)
    {
        $dusun = Dusun::findOrFail($id);
        $dusun->delete();
        return response()->json(null, 204);
    }

    // CRUD for RW
    public function indexRw()
    {
        return response()->json(RW::all());
    }

    public function storeRw(Request $request)
    {
        $request->validate([
            'No_RW' => 'required|integer',
            'dusun_id' => 'required|exists:dusun,id'
        ]);

        $rw = RW::create($request->all());
        return response()->json($rw, 201);
    }

    public function showRw($id)
    {
        $rw = RW::findOrFail($id);
        return response()->json($rw);
    }

    public function updateRw(Request $request, $id)
    {
        $request->validate([
            'No_RW' => 'required|integer',
            'dusun_id' => 'required|exists:dusun,id'
        ]);

        $rw = RW::findOrFail($id);
        $rw->update($request->all());
        return response()->json($rw);
    }

    public function destroyRw($id)
    {
        $rw = RW::findOrFail($id);
        $rw->delete();
        return response()->json(null, 204);
    }

    // CRUD for RT
    public function indexRt()
    {
        return response()->json(RT::all());
    }

    public function storeRt(Request $request)
    {
        $request->validate([
            'No_RT' => 'required|integer',
            'rw_id' => 'required|exists:rw,id'
        ]);

        $rt = RT::create($request->all());
        return response()->json($rt, 201);
    }

    public function showRt($id)
    {
        $rt = RT::findOrFail($id);
        return response()->json($rt);
    }

    public function updateRt(Request $request, $id)
    {
        $request->validate([
            'No_RT' => 'required|integer',
            'rw_id' => 'required|exists:rw,id'
        ]);

        $rt = RT::findOrFail($id);
        $rt->update($request->all());
        return response()->json($rt);
    }

    public function destroyRt($id)
    {
        $rt = RT::findOrFail($id);
        $rt->delete();
        return response()->json(null, 204);
    }
}
