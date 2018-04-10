<?php 
namespace App\Validate;

use App\Validate\Validator;

class Member extends Validator
{
    protected $messages = [
        'username' => [
            'notEmpty' => '用户名不能为空',
            'length' => '用户名太长',
        ],
        'nickname' => [
            'length' => '昵称太长',
            'notEmpty' => '用户名不能为空',
        ],
    ];
}
