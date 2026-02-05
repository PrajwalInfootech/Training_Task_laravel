<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function noCache($response)
    {
        return $response->headers->set(
            'Cache-Control',
            'no-cache, no-store, max-age=0, must-revalidate'
        );
    }
}
