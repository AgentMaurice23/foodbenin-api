<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentWebhookRequest;
use App\Models\Payment;
use App\Services\Payment\PaymentWebhookService;
use Illuminate\Http\JsonResponse;

/**
 * Handle incoming payment provider webhooks.
 */
class PaymentWebhookController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected PaymentWebhookService $webhookService
    ) {
    }

    /**
     * Handle an incoming payment webhook.
     */
    public function handle(
        PaymentWebhookRequest $request
    ): JsonResponse {
        $payment = Payment::query()
            ->findOrFail(
                $request->integer('payment_id')
            );

        $event = $this->webhookService->process(
            provider: $request->string('provider')->toString(),
            eventId: $request->string('event_id')->toString(),
            eventType: $request->string('event_type')->toString(),
            payment: $payment,
            payload: $request->input('payload'),
            transactionReference: $request->input(
                'transaction_reference'
            ),
            amount: $request->input('amount'),
        );

        return response()->json([
            'success' => true,

            'data' => [
                'event_id' => $event->event_id,
                'provider' => $event->provider,
                'processed' => $event->processed_at !== null,
                'processed_at' => $event->processed_at,
            ],
        ]);
    }
}