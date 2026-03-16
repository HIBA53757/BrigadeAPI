<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plat;
use Illuminate\Http\Request;

class PlatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
{
    $plats = Plat::where('restaurant_id', auth()->id())->get();
    return response()->json($plats);
}

   
  public function store(Request $request)
{
    $request->validate([
        'nom' => 'required',
        'description' => 'nullable',
        'prix' => 'required|numeric'
    ]);

    $plat = Plat::create([
        'nom' => $request->nom,
        'description' => $request->description,
        'prix' => $request->prix,
        'restaurant_id' => auth()->id()
    ]);

    return response()->json($plat, 201);
}  

 public function show($id)
{
    $plat = Plat::findOrFail($id);
    return response()->json($plat);
}

public function update(Request $request, Plat $plat)
{
    $this->authorize('update', $plat);

    $plat->update($request->only(['nom','description','prix']));

    return response()->json($plat);
}

public function destroy(Plat $plat)
{
    $this->authorize('delete', $plat);

    $plat->delete();

    return response()->json(['message' => 'Plat supprimé']);
}
}
