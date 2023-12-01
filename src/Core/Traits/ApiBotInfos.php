<?php

namespace Mmb\Laravel\Core\Traits;

use Mmb\Laravel\Core\Updates\Infos\UserInfo;

trait ApiBotInfos
{

    /**
     * Get robot info
     *
     * @return ?UserInfo
     */
    public function getMe()
    {
        return $this->makeData(
            UserInfo::class,
            $this->request('getMe', [])
        );
    }

}
