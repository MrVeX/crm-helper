<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Log\Logger;
use App\Helper\Crm\CrmHelper;
use App\Service\Crm\CrmTokenService;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function checkStatus(): void
    {
        (new Response(['status' => 'OK'], Response::HTTP_OK))->send();

    }

    public function widgetRequest(Request $request, Logger $logger): void
    {
        $req = ['data' => ['status' => 'success']];
        (new Response($req, Response::HTTP_OK))->send();

        CrmHelper::getApiClient()->getOAuthClient()->getAuthorizationHeaders(CrmTokenService::getToken());
        $content = $request->toArray();
        $logger->info(var_export($content, true));
    }
}
