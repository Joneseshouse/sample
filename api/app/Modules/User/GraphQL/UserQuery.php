<?php

namespace App\Modules\User\GraphQL;

use Folklore\GraphQL\Support\Query as BaseQuery;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;


use App\Modules\User\Models\User;

class UserQuery extends BaseQuery{
    protected $attributes = [
        'name' => 'UserQuery',
        'description' => 'A query'
    ];

    protected function type(){
        # return Type::listOf(Type::string());
        return GraphQL::type('UserType');
    }

    protected function args(){
        return [
           'id' => [
                'name' => 'id',
                'type' => Type::nonNull(Type::id()),
                'description' => 'The user id'
            ]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info){
        $user = User::find($args['id']);
        $user->role_uid = $user->role->uid;
        return $user;
    }
}
