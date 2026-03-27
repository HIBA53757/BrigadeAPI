<?php

namespace App\Policies;

use App\Models\Ingredient;
use App\Models\Ingredients;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IngredientPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; 
    }

    public function view(User $user, Ingredients $ingredient): bool
    {
        return true; 
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Ingredients $ingredient): bool
    {
        return $user->role === 'admin'; 
    }

    public function delete(User $user, Ingredients $ingredient): bool
    {
        return $user->role === 'admin';
    }
}