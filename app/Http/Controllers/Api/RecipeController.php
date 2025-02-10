<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\RecipeRequest;
use App\Services\RecipeService;
use Illuminate\Http\JsonResponse;

class RecipeController extends MasterApiController
{
    protected RecipeService $recipeService;
    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    /**
     * Get all recipes
     * @response array{success:boolean,message:string,data:App\Http\Resources\RecipeResource[]}
     */
    public function index(): JsonResponse
    {
        return $this->recipeService->index();
    }


    /**
     * Get recipe by id
     * @response array{success:boolean,message:string,data:App\Http\Resources\RecipeResource}
     */
    public function findById(int $id): JsonResponse
    {
        return $this->recipeService->findById($id);
    }

    /**
     * Store a new recipe
     * @response array{success:boolean,message:string,data:App\Http\Resources\RecipeResource}
     */
    public function store(RecipeRequest $request): JsonResponse
    {
        return $this->recipeService->store($request->validated());
    }

    /**
     * Updae a  recipe
     * @response array{success:boolean,message:string,data:App\Http\Resources\RecipeResource}
     */
    public function update(RecipeRequest $request, int $id): JsonResponse
    {
        return $this->recipeService->update($request->validated(), $id);
    }

    /**
     * Delete a recipe
     * @response array{success:boolean,message:string,data:array}
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->recipeService->destroy($id);
    }

}
