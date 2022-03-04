<?php

namespace App\GraphQL\Queries;

use App\Helpers\CatHelper;

class Status
{
    /**
     * @param null $_
     * @param array<string, mixed> $args
     */
    public function __invoke($_, array $args): static
    {
        return $this;
    }

    public function cat(): string
    {
        return CatHelper::getCat();
    }

    public function cattr(): bool
    {
        return true;
    }
}
