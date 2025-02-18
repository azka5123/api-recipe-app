<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * get list all recipes
     */
    public function test_get_list_all_recipes(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/recipes');

        $response->assertStatus(200);
    }

    public function test_store_recipes(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->storeRecipe($user, $token);
        $response->assertStatus(201);

        $this->assertDatabaseHas('recipes', [
            'name' => 'Nasi Goreng Spesial',
            'description' => 'Resep nasi goreng enak dan praktis.',
        ]);
    }

    public function test_get_recipe_by_id(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $recipeStore = $this->storeRecipe($user, $token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/recipes/' . $recipeStore->json('data.id'));
        $response->assertStatus(200);
    }

    public function test_update_recipes(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $storeRecipe = $this->storeRecipe($user, $token);

        $updateData = [
            'name' => 'Nasi Goreng Spesial update',
            'description' => 'Resep nasi goreng enak dan praktis.',
            'cooking_duration' => 30,
            'user_id' => $user->id,
            'ingredients' => [
                ['name' => 'Nasi'],
                ['name' => 'Telur'],
                ['name' => 'Bawang'],
            ],
            'steps' => [
                [
                    'description' => 'Panaskan minyak dan tumis bawang.',
                    'step_order' => 1
                ],
                [
                    'description' => 'Masukkan telur dan aduk hingga matang.',
                    'step_order' => 2
                ],
                [
                    'description' => 'Tambahkan nasi, aduk rata, lalu sajikan.',
                    'step_order' => 3
                ],
            ],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/recipes/update/' . $storeRecipe->json('data.id'), $updateData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('recipes', [
            'name' => 'Nasi Goreng Spesial update',
            'description' => 'Resep nasi goreng enak dan praktis.',
        ]);
    }

    public function test_delete_recipes(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->plainTextToken;

        $recipeStore = $this->storeRecipe($user, $token);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/recipes/destroy/' . $recipeStore->json('data.id'));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('recipes', [
            'name' => 'Nasi Goreng Spesial',
            'description' => 'Resep nasi goreng enak dan praktis.',
        ]);
    }

    private function storeRecipe($user, $token)
    {
        $recipeData = [
            'name' => 'Nasi Goreng Spesial',
            'description' => 'Resep nasi goreng enak dan praktis.',
            'cooking_duration' => 30,
            'user_id' => $user->id,
            'ingredients' => [
                ['name' => 'Nasi'],
                ['name' => 'Telur'],
                ['name' => 'Bawang'],
            ],
            'steps' => [
                [
                    'description' => 'Panaskan minyak dan tumis bawang.',
                    'step_order' => 1
                ],
                [
                    'description' => 'Masukkan telur dan aduk hingga matang.',
                    'step_order' => 2
                ],
                [
                    'description' => 'Tambahkan nasi, aduk rata, lalu sajikan.',
                    'step_order' => 3
                ],
            ],
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/recipes/store', $recipeData);

        return $response;
    }

}
