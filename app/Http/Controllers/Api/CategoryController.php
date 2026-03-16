<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Lister les catégories de l'utilisateur connecté
    public function index()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        return response()->json($categories);
    }

    // Créer une catégorie
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category = Category::create([
            'name' => $request->name,
            'user_id' => auth()->id(),
        ]);

        return response()->json($category, 201);
    }

    // Afficher une catégorie spécifique
    public function show(Category $category)
    {
        return response()->json($category);
    }

    // Modifier une catégorie
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $category->update($request->only('name'));

        return response()->json($category);
    }

    // Supprimer une catégorie
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }

    // Associer des plats à une catégorie
    public function addPlats(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $request->validate([
            'plats' => 'required|array'
        ]);

        $category->plats()->sync($request->plats);

        return response()->json(['message' => 'Plats added to category']);
    }
}