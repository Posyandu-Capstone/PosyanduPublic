<?php
namespace App\Http\Controllers\Location;

use App\Models\Kecamatan;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class KecamatanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return response()->json(Kecamatan::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama_Kecamatan' => 'required|string|max:255'
        ]);

        $kecamatan = Kecamatan::create($request->all());
        return response()->json($kecamatan, 201);
    }

    public function show($id)
    {
        $kecamatan = Kecamatan::findOrFail($id);
        return response()->json($kecamatan);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama_Kecamatan' => 'required|string|max:255'
        ]);

        $kecamatan = Kecamatan::findOrFail($id);
        $kecamatan->update($request->all());
        return response()->json($kecamatan);
    }

    public function destroy($id)
    {
        $kecamatan = Kecamatan::findOrFail($id);
        $kecamatan->delete();
        return response()->json(null, 204);
    }
}
