<?php

namespace App\GraphQL\Schemas;

use App\GraphQL\Queries\User\GetCurrent;
use App\GraphQL\Queries\User\ListAll;
use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class UserSchema implements ConfigConvertible
{
    /**
     * @inheritDoc
     */
    public function toConfig(): array
    {
        return [
            'query' => [
                ListAll::class,
                GetCurrent::class
            ],
            'method' => ['POST'],
        ];
    }
}
