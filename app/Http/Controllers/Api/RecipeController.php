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
        // dd($request->validated());
        return $this->recipeService->store($request->validated());
    }
}
