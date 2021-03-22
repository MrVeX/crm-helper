<?php

declare(strict_types=1);

namespace App\AmoCrm\Entities;

use AmoCRM\EntitiesServices\BaseEntity;
use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Client\AmoCRMApiRequest;

abstract class Bot extends BaseEntity
{
    /** @var string {marketingbot|salesbot} */
    protected string $bot = '';

    protected string $method = 'api/v' . AmoCRMApiClient::API_VERSION . '/';

    public function __construct(AmoCRMApiRequest $request)
    {
        parent::__construct($request);

        $this->method .= $this->bot . '/';
    }

    public function continueBotByLink(string $link, array $body = []): array
    {
        return $this->request->post($link, $body);
    }

    public function continueBot(int $botId, int $continueId, array $body = []): array
    {
        $link = $this->method . $botId . '/continue/' . $continueId;

        return $this->request->post($link, $body);
    }
}