<?php

namespace App\Services;

use App\Helpers\GlobalFunction;
use App\Helpers\ResponseHelper;
use App\Repositories\RecipeRepository;
use File;
use Illuminate\Http\JsonResponse;
use Log;

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
        // Set the public file path for the recipe image
        // e.g. img/recipes_image/1/
        $this->filePath = "uploads/img/recipes_image/{$userId}/";
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

            // Store the recipe in the database
            $recipe = $this->recipeRepository->store($data);

            // Return a success response with the recipe
            return ResponseHelper::success("success store recipe", $recipe);
        } catch (\Exception $e) {
            // If any error, delete the uploaded images
            foreach ($uploadedImages as $image) {
                if (file_exists($image)) {
                    unlink($image);
                }
            }
            // Return any error response with a 500 status code
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    public function update(array $data, int $id): JsonResponse
    {
        $uploadedImages = [];
        try {
            $recipe = $this->recipeRepository->findById($id);
            $removeImageExtension = preg_replace('/\.[^.]+$/', '', $recipe->image);
            if ($data['image']) {
                $imagePath = $this->filePath . $removeImageExtension;
                $deleteImagePath = $this->filePath . $removeImageExtension . '/' . $recipe->image;
                GlobalFunction::deleteSingleImage($deleteImagePath);
                $data['image'] = GlobalFunction::handleImageUpload($data['image'], $removeImageExtension, $imagePath);
                $uploadedImages[] = $imagePath . '/' . $data['image'];
            }

            foreach ($data['steps'] as $index => $step) {
                if (!empty($step['image'])) {
                    $stepImagePath = $this->filePath . $removeImageExtension . '/steps_image';
                    $existingStepImage = $recipe->rStep[$index]->image ?? null;
                    if ($existingStepImage) {
                        $deleteStepImagePath = $this->filePath . $removeImageExtension . '/steps_image/' . $existingStepImage;
                        GlobalFunction::deleteSingleImage($deleteStepImagePath);
                    }
                    $data['steps'][$index]['image'] = GlobalFunction::handleImageUpload(
                        $step['image'],
                        'step_' . $step['step_order'],
                        $stepImagePath
                    );
                    $uploadedImages[] = $stepImagePath . '/' . $data['steps'][$index]['image'];
                }
            }

            $recipe = $this->recipeRepository->update($data, $id);

            return ResponseHelper::success("success update recipe", $recipe);
        } catch (\Exception $e) {
            foreach ($uploadedImages as $image) {
                if (file_exists($image)) {
                    unlink($image);
                }
            }
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }

    /**
     * Destroy a recipe
     *
     * @param int $id The id of the recipe
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Find the recipe by id
            $recipe = $this->recipeRepository->findById($id);

            // If the recipe is not found, return a 404 error
            if (!$recipe) {
                return ResponseHelper::error("Recipe not found", 404);
            }

            // Remove the folder and subfolder of the recipe image
            $removeImageExtension = preg_replace('/\.[^.]+$/', '', $recipe->image);
            $removeFolder = public_path($this->filePath . $removeImageExtension);

            // Check if the folder exists
            if (file_exists($removeFolder)) {
                // Delete the folder
                File::deleteDirectory($removeFolder);
            }

            // Delete the recipe from the database
            $this->recipeRepository->destroy($id);

            // Return a success response
            return ResponseHelper::success("success delete recipe", []);
        } catch (\Exception $e) {
            // If any error, return a 500 error
            return ResponseHelper::error($e->getMessage(), 500);
        }
    }
}