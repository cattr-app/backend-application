<?php

namespace App\Exceptions\Entities;

use Flugg\Responder\Exceptions\Http\HttpException;

class GraphQLBatchingException extends HttpException
{
    protected $status = 400;

    protected $errorCode = 'http.request.batching_not_supported';
}
