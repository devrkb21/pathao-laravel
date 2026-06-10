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
        $signature = $request->header('X-Pathao-Signature');
        $expectedSecret = config('pathao.pathao_secret_token');
        $payload = $request->all();
        $signatureValid = (! empty($signature) && ! empty($expectedSecret) && $signature === $expectedSecret);

        // Log incoming webhook to DB
        try {
            DB::table('pathao_webhook_logs')->insert([
                'event' => $payload['event'] ?? null,
                'consignment_id' => $payload['consignment_id'] ?? null,
                'merchant_order_id' => $payload['merchant_order_id'] ?? null,
                'payload' => json_encode($payload),
                'signature_valid' => $signatureValid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silence logging exceptions
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
        ], 202)->header('X-Pathao-Merchant-Webhook-Integration-Secret', $expectedSecret);
    }
}
