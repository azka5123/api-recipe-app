<?php

namespace App\Services;

use App\Helpers\GlobalFunction;
use App\Helpers\ResponseHelper;
use App\Repositories\RecipeRepository;
use Illuminate\Http\JsonResponse;

class RecipeService
{
    protected RecipeRepository $recipeRepository;
    public $filePath;
    /**
     * RecipeService constructor.
     *
     * @param RecipeRepository $recipeRepository
     */
    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
        $userId = auth()->id();
        // Set the file path for the recipe image
        // e.g. img/recipes_image/1/
        $this->filePath = "img/recipes_image/{$userId}/";
    }

    /**
     * Get all recipes
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Get all recipes from the database
            $recipes = $this->recipeRepository->index();
            // Return a success response with the recipes
            return ResponseHelper::success("success get all recipes", $recipes);
        } catch (\Exception $e) {
            // Return any error response with a 500 status code
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    /**
     * Get recipe by id
     *
     * @param int $id
     * @return JsonResponse
     */
    public function findById(int $id): JsonResponse
    {
        try {
            // Get the recipe by id
            $recipes = $this->recipeRepository->findById($id);

            // If not found, return a 404 error
            if (!$recipes) {
                return ResponseHelper::error("recipe not found", 404);
            }

            // Return a success response with the recipe
            return ResponseHelper::success("success get all recipes", $recipes);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // If the recipe not found, return a 404 error
            return ResponseHelper::error("recipe not found", 404);
        } catch (\Exception $e) {
            // If any other error, return a 500 error
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    /**
     * Store a new recipe
     *
     * @param array $data
     * @return JsonResponse
     */
    public function store(array $data): JsonResponse
    {
        $uploadedImages = [];
        try {
            $name = GlobalFunction::genaratorNameFile($data['name']);
            // If the recipe has an image, upload it
            if ($data['image']) {
                $imagePath = $this->filePath . $name;
                $data['image'] = GlobalFunction::handleImageUpload($data['image'], $name, $imagePath);
                $uploadedImages[] = $imagePath . '/' . $data['image'];
            }

            // If the recipe has steps, upload the images of each step
            if (!empty($data['steps'])) {
                foreach ($data['steps'] as $index => $step) {
                    if (!empty($step['image'])) {
                        $stepImagePath = $this->filePath . $name . '/steps_image';
                        $data['steps'][$index]['image'] = GlobalFunction::handleImageUpload(
                            $step['image'],
                            'step_' . $step['step_order'],
                            $stepImagePath
                        );
                        $uploadedImages[] = $stepImagePath . '/' . $data['steps'][$index]['image'];
                    }
                }
            }

            // Store the recipe in the database
            $recipe = $this->recipeRepository->store($data);

            // Return a success response with the recipe
            return ResponseHelper::success("success store recipe", $recipe);
        } catch (\Exception $e) {
            // If any error, delete the uploaded images
            foreach ($uploadedImages as $image) {
                $image = 'uploads/' . $image;
                if (file_exists($image)) {
                    unlink($image);
                }
            }
            // Return any error response with a 500 status code
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }
}