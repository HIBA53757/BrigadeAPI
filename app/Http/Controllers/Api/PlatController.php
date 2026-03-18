<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PlatController extends Controller
{   use AuthorizesRequests;
 
    public function index()
    {
        $plats = Plat::where('user_id', auth()->id())->get();
        return response()->json($plats);
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'prix' => 'required|numeric|min:0',
        ]);

        $plat = Plat::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'prix' => $request->prix,
            'user_id' => auth()->id(), 
        ]);

        return response()->json($plat, 201);
    }


    public function show(Plat $plat)
    {
        $this->authorize('view', $plat); 
        return response()->json($plat);
    }

  
    public function update(Request $request, Plat $plat)
    {
        $this->authorize('update', $plat);

        $request->validate([
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'prix' => 'sometimes|required|numeric|min:0',
        ]);

        $plat->update($request->only(['nom', 'description', 'prix']));

        return response()->json($plat);
    }


    public function destroy(Plat $plat)
    {
        $this->authorize('delete', $plat);

        $plat->delete();

        return response()->json(['message' => 'Plat supprimé']);
    }

    
}