<?php
namespace App\Http\Controllers\Location;

use App\Models\Dusun;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class DusunController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return response()->json(Dusun::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama_Dusun' => 'required|string|max:255',
            'desa_id' => 'required|exists:desa,id'
        ]);

        $dusun = Dusun::create($request->all());
        return response()->json($dusun, 201);
    }

    public function show($id)
    {
        $dusun = Dusun::findOrFail($id);
        return response()->json($dusun);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama_Dusun' => 'required|string|max:255',
            'desa_id' => 'required|exists:desa,id'
        ]);

        $dusun = Dusun::findOrFail($id);
        $dusun->update($request->all());
        return response()->json($dusun);
    }

    public function destroy($id)
    {
        $dusun = Dusun::findOrFail($id);
        $dusun->delete();
        return response()->json(null, 204);
    }
}
