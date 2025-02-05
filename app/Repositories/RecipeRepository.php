<?php

namespace App\Repositories;

use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecipeRepository
{

    public function index(): AnonymousResourceCollection
    {
        $recipes = Recipe::with(['rIngredient','rStep','rUser'])->get();
        return RecipeResource::collection($recipes);
    }

    public function findById(int $id): AnonymousResourceCollection
    {
        $recipes = Recipe::with(['rIngredient','rStep','rUser'])->findOrFail($id);
        return RecipeResource::collection($recipes);
    }
}