<?php 
namespace App\Validate;

use App\Validate\Validator;

class Catelog extends Validator
{
    protected $messages = [
        'title' => [
            'notEmpty' => '标题不能为空',
            'length' => '标题太长',
        ],
        'pid' => [
            'intVal' => '类别必须为数字',
            'notEmpty' => '类别不能为空',
        ]
    ];
}
