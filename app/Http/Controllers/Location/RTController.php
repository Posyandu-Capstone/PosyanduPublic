<?php
namespace App\Http\Controllers\Location;

use App\Models\RT;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class RtController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        return response()->json(RT::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'No_RT' => 'required|integer',
            'rw_id' => 'required|exists:rw,id'
        ]);

        $rt = RT::create($request->all());
        return response()->json($rt, 201);
    }

    public function show($id)
    {
        $rt = RT::findOrFail($id);
        return response()->json($rt);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'No_RT' => 'required|integer',
            'rw_id' => 'required|exists:rw,id'
        ]);

        $rt = RT::findOrFail($id);
        $rt->update($request->all());
        return response()->json($rt);
    }

    public function destroy($id)
    {
        $rt = RT::findOrFail($id);
        $rt->delete();
        return response()->json(null, 204);
    }
}
