<?php

declare(strict_types=1);

namespace App\Service\Crm;

use Illuminate\Support\Facades\Storage;
use App\Exceptions\Crm\CrmSaveTokenException;
use App\Exceptions\Crm\CrmGetTokenException;
use League\OAuth2\Client\Token\AccessToken;
use PhpParser\JsonDecoder;
use Psy\Util\Json;

class CrmTokenService
{
    private const CRM_DIRECTORY = 'crm';
    private const CRM_KEYS_FILE = 'keys.json';

    private static string $path = self::CRM_DIRECTORY . DIRECTORY_SEPARATOR . self::CRM_KEYS_FILE;

    public static function saveToken(array $accessToken): bool
    {
        try {
            if (!isset(
                $accessToken['accessToken'],
                $accessToken['refreshToken'],
                $accessToken['expires'],
                $accessToken['baseDomain']
            )) {
                throw new CrmSaveTokenException(
                    'Dont save access token: ' .
                    var_export($accessToken, true) .
                    PHP_EOL . 'Don\'t have any key in array: [accessToken, refreshToken, expires, baseDomain]'
                );
            }

            if (Storage::exists(self::$path)) {
                Storage::delete(self::$path);
            } else {
                Storage::makeDirectory(self::CRM_DIRECTORY);
            }

            $json = Json::encode(
                [
                    'access_token' => $accessToken['accessToken'],
                    'refresh_token' => $accessToken['refreshToken'],
                    'expires' => $accessToken['expires'],
                    'baseDomain' => $accessToken['baseDomain'],
                ]
            );

            $result = Storage::append(self::$path, $json);
        } catch (CrmSaveTokenException $e) {
            $result = false;
        }

        return $result;
    }

    public static function getToken(): ?AccessToken
    {
        try {
            $accessToken = Storage::get(self::$path);

            if (empty($accessToken)) {
                throw new CrmGetTokenException('Empty token file: ' . self::$path);
            }

            $accessToken = (new JsonDecoder())->decode($accessToken);

            if (!isset(
                $accessToken['accessToken'],
                $accessToken['refreshToken'],
                $accessToken['expires'],
                $accessToken['baseDomain']
            )) {
                throw new CrmGetTokenException(
                    'Dont get access token: ' .
                    var_export($accessToken, true) .
                    PHP_EOL . 'Don\'t have any key in array: [accessToken, refreshToken, expires, baseDomain]'
                );
            }

            $result = new AccessToken(
                [
                    'access_token' => $accessToken['accessToken'],
                    'refresh_token' => $accessToken['refreshToken'],
                    'expires' => $accessToken['expires'],
                    'baseDomain' => $accessToken['baseDomain'],
                ]
            );
        } catch (CrmGetTokenException $e) {
            $result = null;
        }

        return $result;
    }

    public static function getTokenRaw(): ?array
    {
        $accessToken = Storage::get(self::$path);
        return empty($accessToken) ? null : (new JsonDecoder())->decode($accessToken);
    }
}