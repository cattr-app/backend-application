<?php

namespace App\Exceptions\Entities;

use Flugg\Responder\Exceptions\Http\HttpException;

class AppInstallationException extends HttpException
{
    protected $status = 400;

    protected $errorCode = 'core.installation';
}
