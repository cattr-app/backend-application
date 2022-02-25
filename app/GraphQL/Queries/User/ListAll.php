<?php

namespace App\GraphQL\Queries\User;

use App\Models\User;
use Closure;
use GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Rebing\GraphQL\Support\Query;

class ListAll extends Query
{
    protected $attributes = [
        'name' => 'users',
    ];

    public function type(): Type
    {
        return GraphQL::paginate('user');
    }

    public function authorize(
        $root,
        array $args,
        $ctx,
        ResolveInfo $resolveInfo = null,
        Closure $getSelectFields = null
    ): bool {
        return auth()->check();
    }

    public function resolve($root, array $args, $context, ResolveInfo $info, Closure $getSelectFields): LengthAwarePaginator
    {
        $fields = $getSelectFields();

        return User::with($fields->getRelations())
                   ->select($fields->getSelect())->paginate();
    }
}
