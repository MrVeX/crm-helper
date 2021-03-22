<?php

namespace App\Http\Controllers\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Helper\Crm\CrmHelper;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use App\Exceptions\CrmException;
use Illuminate\Log\Logger;
use App\Service\Crm\CrmTokenService;

class CrmAuthController extends Controller
{
    public function crmAuth(Request $request, Logger $logger): Response
    {
        $code = $request->get('code');
        $ref = $request->get('referer');

        $response = new Response('', Response::HTTP_CREATED);

        try {
            if (empty($code)) {
                throw new CrmException(
                    'Missing code for getting access token',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $apiClient = CrmHelper::getApiClient();

            if (!empty($ref)) {
                $apiClient->setAccountBaseDomain($ref);
            }

            $accessToken = $apiClient->getOAuthClient()->getAccessTokenByCode($code);

            if (!$accessToken->hasExpired()) {
                CrmTokenService::saveToken(
                    [
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $apiClient->getAccountBaseDomain(),
                    ]
                );
            } else {
                throw new CrmException(
                    'Expired token',
                    Response::HTTP_BAD_REQUEST
                );
            }
        } catch (AmoCRMoAuthApiException | CrmException $e) {
            $logger->info(var_export($request->toArray(), true));

            $response = new Response(
                [
                    'status' => 'fail',
                    'message' => $e->getMessage(),
                ],
                $e->getCode() ?: Response::HTTP_BAD_REQUEST
            );
        }

        return $response;
    }
}
