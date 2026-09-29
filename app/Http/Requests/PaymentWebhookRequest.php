<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validate incoming payment webhook requests.
 */
class PaymentWebhookRequest extends FormRequest
{
    /**
     * Determine whether the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /*
         * Authentication is intentionally not required here.
         *
         * Payment providers call webhook endpoints directly.
         * Provider-specific authentication/signature verification
         * will be handled separately.
         */
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'provider' => [
                'required',
                'string',
                'max:50',
            ],

            'event_id' => [
                'required',
                'string',
                'max:255',
            ],

            'event_type' => [
                'required',
                'string',
                'max:100',
            ],

            'payment_id' => [
                'required',
                'integer',
                'exists:payments,id',
            ],

            'transaction_reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'payload' => [
                'required',
                'array',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'provider.required' =>
                'Le provider de paiement est obligatoire.',

            'event_id.required' =>
                'L\'identifiant de l\'événement est obligatoire.',

            'event_type.required' =>
                'Le type d\'événement est obligatoire.',

            'payment_id.required' =>
                'Le paiement est obligatoire.',

            'payment_id.exists' =>
                'Le paiement indiqué est introuvable.',

            'amount.numeric' =>
                'Le montant du webhook doit être numérique.',

            'amount.min' =>
                'Le montant du webhook doit être positif ou nul.',

            'payload.required' =>
                'Le payload du webhook est obligatoire.',

            'payload.array' =>
                'Le payload du webhook doit être un tableau.',
        ];
    }
}