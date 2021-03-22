<?php

declare(strict_types=1);

namespace App\AmoCrm\Helper;

use App\AmoCRM\Client\AmoCRMApiClient;
use Illuminate\Support\Env;
use App\AmoCrm\Service\CrmTokenService;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Token\AccessToken;

class CrmHelper
{
    private const ENV_CRM_CLIENT_SECRET = 'CRM_CLIENT_SECRET';
    private const ENV_CRM_CLIENT_ID = 'CRM_CLIENT_ID';
    private const ENV_CRM_CLIENT_REDIRECT_URI = 'CRM_CLIENT_REDIRECT_URI';

    public static function getApiClient(): AmoCRMApiClient
    {
        $apiClient = new AmoCRMApiClient(
            Env::get(self::ENV_CRM_CLIENT_ID),
            Env::get(self::ENV_CRM_CLIENT_SECRET),
            Env::get(self::ENV_CRM_CLIENT_REDIRECT_URI)
        );

        $accessToken = CrmTokenService::getToken();

        if ($accessToken instanceof AccessToken) {
            $apiClient->setAccessToken($accessToken)
                ->setAccountBaseDomain($accessToken->getValues()['baseDomain']);
        }

        $apiClient->onAccessTokenRefresh(
            function (AccessTokenInterface $accessToken, string $baseDomain) {
                saveToken(
                    [
                        'accessToken' => $accessToken->getToken(),
                        'refreshToken' => $accessToken->getRefreshToken(),
                        'expires' => $accessToken->getExpires(),
                        'baseDomain' => $baseDomain,
                    ]
                );
            }
        );

        return $apiClient;
    }
}