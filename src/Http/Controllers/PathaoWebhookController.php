<?php

namespace devrkb21\PathaoLaravel\Http\Controllers;

use devrkb21\PathaoLaravel\Events\PathaoWebhookReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class PathaoWebhookController extends Controller
{
    /**
     * Handle the incoming Pathao webhook callback
     *
     * @return JsonResponse
     */
    public function handle(Request $request)
    {
        $expectedSecret = config('pathao.pathao_secret_token');
        $payload = $request->all();
        $signature = $request->header('X-Pathao-Signature');
        
        $signatureValid = (! empty($signature) && ! empty($expectedSecret) && $signature === $expectedSecret);
        $isIntegrationEvent = isset($payload['event']) && $payload['event'] === 'webhook_integration';
        
        // Pathao often sends this secret in the incoming header, otherwise fallback to config
        $integrationSecretHeader = $request->header('X-Pathao-Merchant-Webhook-Integration-Secret') 
            ?: config('pathao.webhook_integration_secret');

        // Log incoming webhook to DB
        try {
            DB::table('pathao_webhook_logs')->insert([
                'event' => $payload['event'] ?? null,
                'consignment_id' => $payload['consignment_id'] ?? null,
                'merchant_order_id' => $payload['merchant_order_id'] ?? null,
                'payload' => json_encode($payload),
                'signature_valid' => $isIntegrationEvent ? true : $signatureValid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silence logging exceptions
        }

        if ($isIntegrationEvent) {
            return response()->json([
                'status' => 202,
                'message' => 'Webhook integration successful',
            ], 202)->header('X-Pathao-Merchant-Webhook-Integration-Secret', $integrationSecretHeader);
        }

        if (! $signatureValid) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Invalid webhook signature',
            ], 401);
        }

        // Dispatch Laravel Event
        event(new PathaoWebhookReceived($payload));

        return response()->json([
            'status' => 202,
            'message' => 'Webhook received successfully',
            'data' => null,
        ], 202)->header('X-Pathao-Merchant-Webhook-Integration-Secret', $integrationSecretHeader);
    }
}
