<?php

namespace App\Repositories;

use App\Http\Resources\RecipeResource;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Step;
use DB;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecipeRepository
{
    /**
     * Get all recipes
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $recipes = Recipe::with(['rIngredient', 'rStep', 'rUser'])->get();
        return RecipeResource::collection($recipes);
    }

    /**
     * Get recipe by id
     * @param int $id The id of the recipe
     * @return \App\Http\Resources\RecipeResource
     */
    public function findById(int $id): RecipeResource
    {
        $recipe = Recipe::with(['rIngredient', 'rStep', 'rUser'])->findOrFail($id);
        return new RecipeResource($recipe);
    }

    /**
     * Store recipe
     * @param array $data
     * @return \App\Http\Resources\RecipeResource The stored recipe
     */
    public function store(array $data): RecipeResource
    {
        return DB::transaction(function () use ($data) {
            $recipe = Recipe::create($data);

            foreach ($data['ingredients'] as $ingredient) {
                Ingredient::create([
                    'recipe_id' => $recipe->id,
                    'name' => $ingredient['name']
                ]);
            }

            foreach ($data['steps'] as $step) {
                Step::create([
                    'recipe_id' => $recipe->id,
                    'description' => $step['description'],
                    'step_order' => $step['step_order'],
                    'image' => $step['image'] ?? null
                ]);
            }

            return new RecipeResource($recipe->load(['rIngredient', 'rStep', 'rUser']));
        });
    }

    public function update(array $data, int $id): RecipeResource
    {
        return DB::transaction(function () use ($data, $id) {
            $recipe = $this->findById($id);
            $recipe->update($data);

            Ingredient::where('recipe_id', $recipe->id)->delete();
            Step::where('recipe_id', $recipe->id)->delete();

            foreach ($data['ingredients'] as $ingredient) {
                Ingredient::create([
                    'recipe_id' => $recipe->id,
                    'name' => $ingredient['name']
                ]);
            }

            foreach ($data['steps'] as $step) {
                Step::create([
                    'recipe_id' => $recipe->id,
                    'description' => $step['description'],
                    'step_order' => $step['step_order'],
                    'image' => $step['image'] ?? null
                ]);
            }

            return new RecipeResource($recipe->load(['rIngredient', 'rStep', 'rUser']));
        });
    }

    /**
     * Delete a recipe
     *
     * @param int $id The id of the recipe to delete
     */
    public function destroy(int $id): void
    {
        $recipe = Recipe::find($id);
        $recipe->delete();
    }
}