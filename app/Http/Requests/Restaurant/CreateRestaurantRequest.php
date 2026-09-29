<?php

namespace App\Http\Requests\Restaurant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateRestaurantRequest extends FormRequest
{
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [

            'name' =>
                'required|max:255',

            'description' =>
                'required',

            'phone' =>
                'required',

            'email' =>
                'required|email',

            'city_id' =>
                'required|exists:cities,id',

            'zone_id' =>
                'required|exists:zones,id',

            'delivery_fee' =>
                'required|numeric',

            'minimum_order' =>
                'required|numeric',
        ];
    }
}
