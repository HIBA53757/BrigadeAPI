<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Plat;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class CategoryController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        return response()->json($categories);
    }

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

    public function show(Category $category)
    {
        return response()->json($category);
    }

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

   
  public function addPlats(Request $request, Category $category)
{
   
    if ($category->user_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $request->validate([
        'plats' => 'required|array',
        'plats.*' => 'exists:plats,id'
    ]);

    
    Plat::whereIn('id', $request->plats)
        ->where('user_id', auth()->id()) 
        ->update(['category_id' => $category->id]);

    return response()->json([
        'message' => 'Plats associés à la catégorie avec succès',
        'category' => $category->load('plats')
    ]);
}
}