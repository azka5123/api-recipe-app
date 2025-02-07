<?php

namespace App\Services;

use App\Helpers\GlobalFunction;
use App\Helpers\ResponseHelper;
use App\Repositories\RecipeRepository;
use Illuminate\Http\JsonResponse;

class RecipeService
{
    protected RecipeRepository $recipeRepository;
    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    public function index(): JsonResponse
    {
        try {
            $recipes = $this->recipeRepository->index();
            return ResponseHelper::success("success get all recipes", $recipes);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function findById(int $id): JsonResponse
    {
        try {
            $recipes = $this->recipeRepository->findById($id);
            if (!$recipes) {
                return ResponseHelper::error("recipe not found", 404);
            }
            return ResponseHelper::success("success get all recipes", $recipes);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ResponseHelper::error("recipe not found", 404);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            if ($data['image']) {
                $data['image'] = GlobalFunction::handleImageUpload($data['image'], $data['name'], 'recipes_image');
            }
            if (!empty($data['steps'])) {
                foreach ($data['steps'] as $index => $step) {
                    if (!empty($step['image'])) {
                        $data['steps'][$index]['image'] = GlobalFunction::handleImageUpload($step['image'], 'step_' . $step['step_order'], 'steps_image');
                    }
                }
            }
            dd($data);
            $recipes = $this->recipeRepository->store($data);
            return ResponseHelper::success("success store recipe", $recipes);
        } catch (\Exception $e) {
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }
}