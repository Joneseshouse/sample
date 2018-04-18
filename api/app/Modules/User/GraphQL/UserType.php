<?php

namespace App\Modules\User\GraphQL;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class UserType extends BaseType{
    protected $attributes = [
        'name' => 'UserType',
        'description' => 'A type'
    ];

    protected function fields(){
        return [
        	'id' => [
                'type' => Type::id(),
                'description' => 'The user id'
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'The user email'
            ],
            'full_name' => [
                'type' => Type::string(),
                'description' => 'The user fullname'
            ],
            'role_uid' => [
                'type' => Type::string(),
                'description' => 'The user role'
            ],
            'permissions' => [
                'type' => Type::string(),
                'description' => 'The user permissions'
            ]
        ];
    }
}
