<?php

namespace App\GraphQL\Types;

use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes
        = [
            'name' => 'user',
            'description' => 'Cattr user',
            'model' => User::class,
        ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the user',
            ],
            'email' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'User email',
            ]
        ];
    }
}
