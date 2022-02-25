<?php

namespace App\Exceptions\Entities;

use Flugg\Responder\Exceptions\Http\HttpException;

class DatabaseException extends HttpException
{
    protected $errorCode = 'core.database.error';
}
