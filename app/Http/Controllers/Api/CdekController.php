<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CdekService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CdekController extends Controller
{
    public function __construct(
        private readonly CdekService $cdekService,
    ) {
    }

    public function proxy(Request $request, string $endpoint): JsonResponse
    {
        $allowedEndpoints = [
            'calculator/tarifflist',
            'deliverypoints',
            'location/cities',
        ];

        if (! in_array($endpoint, $allowedEndpoints, true)) {
            return response()->json(['error' => 'Endpoint not allowed'], 403);
        }

        $method = $request->method();
        $data = $request->all();

        $result = $this->cdekService->proxyRequest($method, $endpoint, $data);

        return response()->json($result);
    }

    public function token(): JsonResponse
    {
        $token = $this->cdekService->getToken();

        if (! $token) {
            return response()->json(['error' => 'Failed to get token'], 500);
        }

        return response()->json(['access_token' => $token, 'token_type' => 'bearer']);
    }
}
