<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use app\Models\ingredients;
use app\Models\recommendations;
use app\Models\Category;

class Plat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'prix',
        'user_id',
        'category_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
   public function category()
{
    return $this->belongsTo(Category::class);
}
public function ingredients() {
    return $this->belongsToMany(ingredients::class);
}

public function recommendations() {
    return $this->hasMany(recommendations::class);
}

}