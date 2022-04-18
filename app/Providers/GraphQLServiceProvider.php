<?php

namespace App\Providers;

use App\Enums\UserType;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Exceptions\DefinitionException;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Schema\Types\LaravelEnumType;

class GraphQLServiceProvider extends ServiceProvider
{
    /**
     * @throws DefinitionException
     */
    public function boot(TypeRegistry $registry): void
    {
        $registry->register(new LaravelEnumType(UserType::class));
    }
}
