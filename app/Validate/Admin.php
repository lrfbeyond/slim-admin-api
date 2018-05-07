<?php 
namespace App\Validate;

use App\Validate\Validator;

class Admin extends Validator
{
    protected $messages = [
        'password' => [
            'notEmpty' => '原密码不能为空',
            'length' => '新密码长度应该在6-16位间',
        ],
        'newpass' => [
            'length' => '新密码长度应该在6-16位间',
            'notEmpty' => '新密码不能为空',
        ],
        'admin_name' => [
            'length' => '账号长度应该在6-16位间',
            'notEmpty' => '账号不能为空',
        ],
        'role_id' => [
            'intVal' => '用户组必须为数字',
            'notEmpty' => '请选择用户组',
        ],
    ];
}

