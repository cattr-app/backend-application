<?php

namespace App\Exceptions\Entities;

use Flugg\Responder\Exceptions\Http\HttpException;

class GraphQLRequestException extends HttpException
{
    protected $status = 400;

    protected $errorCode = 'http.request.graphql';

    public function __construct(?array $errors)
    {
        $this->data['errors'] = $errors;

        parent::__construct();
    }
}
