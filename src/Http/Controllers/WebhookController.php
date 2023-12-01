<?php

namespace Mmb\Laravel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Mmb\Laravel\Core\Bot;
use Mmb\Laravel\Core\Updates\Update;

class WebhookController extends Controller
{

    public function update(string $token, Request $request)
    {
        if($updateData = $request->json())
        {
            try
            {
                $update = Update::make($updateData, trustedData: true);
            }
            catch(\Exception $exception)
            {
                return "";
            }

            $update->handle();
        }

        return "";
    }

}
