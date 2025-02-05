<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'image',
        'cooking_duration'
    ];

    public function rUser(){
        return $this->belongsTo(User::class);
    }

    public function rIngredient(){
        return $this->hasMany(Ingredient::class);
    }

    public function rStep(){
        return $this->hasMany(Step::class);
    }
}
