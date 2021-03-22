<?php

namespace App\AmoCrm\Entities;

class MarketingBot extends Bot
{
    protected string $bot = 'marketingbot';

    /**
     * Такой метод не реализован ещё, оставляю пустым так как он необходим
     * @param array $response
     * @return array
     */
    protected function getEntitiesFromResponse(array $response): array
    {
        return [];
    }
}