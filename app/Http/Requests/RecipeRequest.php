<?php

namespace App\Http\Requests;

use App\Helpers\ResponseHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RecipeRequest extends FormRequest
{

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()
        ], 422));
    }


    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cooking_duration' => 'required|integer',
            'user_id' => 'required|exists:users,id',

            // Validasi untuk array ingredients
            'ingredients' => 'required|array',
            'ingredients.*.name' => 'required|string',

            // Validasi untuk array steps
            'steps' => 'required|array',
            'steps.*.description' => 'required|string',
            'steps.*.image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'steps.*.step_order' => 'required|integer',
        ];
    }

}
