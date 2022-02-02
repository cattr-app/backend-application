<?php

namespace App\GraphQL\Schemas;

use Rebing\GraphQL\Support\Contracts\ConfigConvertible;

class DefaultSchema implements ConfigConvertible
{
    /**
     * @inheritDoc
     */
    public function toConfig(): array
    {
        return [
            'types' => [
            ],
        ];
    }
}
