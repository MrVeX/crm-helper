<?php

declare(strict_types=1);

namespace App\AmoCrm\Client;

use App\AmoCrm\Entities\MarketingBot;

class AmoCRMApiClient extends \AmoCRM\Client\AmoCRMApiClient
{
    public function marketingBot()
    {
        return new MarketingBot($this->getRequest());
    }
}