<?php

namespace App\Http\Responses;

use App\Exceptions\Entities\GraphQLRequestException;
use Illuminate\Http\JsonResponse;
use Nuwave\Lighthouse\Support\Contracts\CreatesResponse;
use Throwable;

class FractalResponse implements CreatesResponse
{
    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function createResponse(array $result): JsonResponse
    {
        throw_unless(optional($result)['data'], GraphQLRequestException::class, optional($result)['errors']);

        return responder()->success(optional($result)['data'])->respond();
    }
}
