<?php

declare(strict_types=1);

namespace Ciareis\Bypass;

use Exception;

class RouteNotCalledException extends Exception
{
    protected $message = "Route not called";
    protected $code = 500;
}
