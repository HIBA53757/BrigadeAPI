<?php

namespace App\Policies;

use App\Models\Plat;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlatPolicy
{
    use HandlesAuthorization;

   
    public function viewAny(User $user): bool
    {
        
        return true;
    }


    public function view(User $user, Plat $plat): bool
    {
       
        return true;
    }

    public function create(User $user): bool
    {
       
        return $user->role === 'admin';
    }

    
    public function update(User $user, Plat $plat): bool
    {
       
        return $user->role === 'admin' && $user->id === $plat->user_id;
    }

 
    public function delete(User $user, Plat $plat): bool
    {
    
        return $user->role === 'admin' && $user->id === $plat->user_id;
    }

    public function restore(User $user, Plat $plat): bool
    {
        return false;
    }

    public function forceDelete(User $user, Plat $plat): bool
    {
        return false;
    }
}