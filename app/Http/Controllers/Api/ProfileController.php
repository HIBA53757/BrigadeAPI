<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
   
    public function show(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'dietary_tags' => $request->user()->dietary_tags ?? []
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'dietary_tags' => 'required|array',
            'dietary_tags.*' => 'string|in:vegan,no_sugar,no_cholesterol,gluten_free,no_lactose'
        ]);

        $user = $request->user();
        
        $user->update([
            'dietary_tags' => $request->dietary_tags
        ]);

        return response()->json([
            'message' => 'Profil alimentaire mis à jour avec succès',
            'user' => $user
        ]);
    }
}