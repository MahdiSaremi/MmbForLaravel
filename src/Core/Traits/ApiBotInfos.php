<?php

namespace Mmb\Laravel\Core\Traits;

use Mmb\Laravel\Core\Updates\Infos\UserInfo;
use Mmb\Laravel\Core\Updates\Webhooks\WebhookInfo;

trait ApiBotInfos
{

    public function getMe() : ?UserInfo
    {
        return $this->makeData(
            UserInfo::class,
            $this->request('getMe', [])
        );
    }

    public function getWebhookInfo()
    {
        return $this->makeData(
            WebhookInfo::class,
            $this->request('getWebhookInfo', []),
        );
    }

}