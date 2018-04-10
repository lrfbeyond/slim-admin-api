<?php 
namespace App\Validate;

use App\Validate\Validator;

class Admin extends Validator
{
    protected $messages = [
        'password' => [
            'notEmpty' => '原密码不能为空',
        ],
        'newpass' => [
            'length' => '新密码长度应该在6-16位间',
            'notEmpty' => '新密码不能为空',
        ],
    ];
}

