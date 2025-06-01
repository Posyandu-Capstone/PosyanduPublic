<?php
namespace App\Http\Controllers\Location;

use App\Models\RW;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class RwController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return response()->json(RW::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'No_RW' => 'required|integer',
            'dusun_id' => 'required|exists:dusun,id'
        ]);

        $rw = RW::create($request->all());
        return response()->json($rw, 201);
    }

    public function show($id)
    {
        $rw = RW::findOrFail($id);
        return response()->json($rw);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'No_RW' => 'required|integer',
            'dusun_id' => 'required|exists:dusun,id'
        ]);

        $rw = RW::findOrFail($id);
        $rw->update($request->all());
        return response()->json($rw);
    }

    public function destroy($id)
    {
        $rw = RW::findOrFail($id);
        $rw->delete();
        return response()->json(null, 204);
    }
}
