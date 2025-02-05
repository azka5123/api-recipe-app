<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'recipe_id',
        'name',
    ] ;

    public function rRecipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
