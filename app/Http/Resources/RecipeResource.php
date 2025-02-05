<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            "name"=> $this->name,
            "description"=> $this->description,
            "image"=> $this->image,
            "cooking_duration"=> $this->cooking_duration,
            "ingredients"=> IngredientResource::collection($this->whenLoaded('rIngredient')),
            "steps" => StepResource::collection($this->whenLoaded("rStep")),
            "user"=> new UserResource($this->whenLoaded("rUser")),
        ];
    }
}
