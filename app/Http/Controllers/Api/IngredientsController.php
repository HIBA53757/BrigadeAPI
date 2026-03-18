<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\ingredients;
use App\Models\Plat;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    
    public function index()
    {
        return response()->json(Ingredients::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:ingredients',
            'tags' => 'required|array',
            'tags.*' => 'string|in:contains_meat,contains_sugar,contains_cholesterol,contains_gluten,contains_lactose'
        ]);

        $ingredient = ingredients::create($request->all());

        return response()->json($ingredient, 201);
    }

    public function attachToPlat(Request $request, Plat $plat)
    {
        $request->validate([
            'ingredient_ids' => 'required|array',
            'ingredient_ids.*' => 'exists:ingredients,id'
        ]);

      
        $plat->ingredients()->sync($request->ingredient_ids);

        return response()->json([
            'message' => 'Ingrédients liés au plat avec succès',
            'plat' => $plat->load('ingredients')
        ]);
    }
}