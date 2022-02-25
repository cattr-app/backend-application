<?php

namespace App\GraphQL\Queries\User;

use App\Models\User;
use Closure;
use GraphQL;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Query;

class GetCurrent extends Query
{
    protected $attributes = [
        'name' => 'current',
    ];

    public function type(): Type
    {
        return GraphQL::type('user');
    }

    public function authorize(
        $root,
        array $args,
        $ctx,
        ResolveInfo $resolveInfo = null,
        Closure $getSelectFields = null
    ): bool {
        return !auth()->guest();
    }

    public function resolve(): User
    {
        return request()->user();
    }
}
