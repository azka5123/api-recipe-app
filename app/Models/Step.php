<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    protected $fillable = [
        'recipe_id',
        'description',
        'image',
        'step_order',
    ];

    public function rRecipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
