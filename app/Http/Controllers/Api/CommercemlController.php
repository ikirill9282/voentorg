<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Commerceml\CommercemlService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommercemlController extends Controller
{
    public function handle(Request $request, CommercemlService $service): Response
    {
        if (! config('commerceml.enabled')) {
            return response("failure\nExchange is disabled", 403);
        }

        $type = $request->query('type', '');
        $mode = $request->query('mode', '');
        $filename = $request->query('filename', '');

        return match ("{$type}.{$mode}") {
            'catalog.checkauth', 'sale.checkauth' => $service->checkAuth($request),
            'catalog.init', 'sale.init'           => $service->init(),
            'catalog.file'                        => $service->uploadFile($request, $filename, 'catalog'),
            'sale.file'                           => $service->uploadFile($request, $filename, 'sale'),
            'catalog.import'                      => $service->importFile($filename),
            'sale.query'                          => $service->exportOrders(),
            'sale.success'                        => $service->confirmOrderExchange(),
            default                               => response("failure\nUnknown mode: {$type}.{$mode}", 400),
        };
    }
}
