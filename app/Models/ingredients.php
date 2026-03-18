<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ingredients extends Model
{
    protected $fillable = [
    'name',
    'tags', 
];
    protected $casts = ['tags' => 'array'];

public function plats() {
    return $this->belongsToMany(Plat::class);
}
}
