<?php
namespace App\Http\Controllers\Location;

use App\Models\Desa;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class DesaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return response()->json(Desa::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama_Desa' => 'required|string|max:255',
            'kecamatan_id' => 'required|exists:kecamatan,id'
        ]);

        $desa = Desa::create($request->all());
        return response()->json($desa, 201);
    }

    public function show($id)
    {
        $desa = Desa::findOrFail($id);
        return response()->json($desa);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama_Desa' => 'required|string|max:255',
            'kecamatan_id' => 'required|exists:kecamatan,id'
        ]);

        $desa = Desa::findOrFail($id);
        $desa->update($request->all());
        return response()->json($desa);
    }

    public function destroy($id)
    {
        $desa = Desa::findOrFail($id);
        $desa->delete();
        return response()->json(null, 204);
    }
}
