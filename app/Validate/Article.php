<?php 
namespace App\Validate;

use App\Validate\Validator;

class Article extends Validator
{
    protected $messages = [
        'title' => [
            'notEmpty' => '标题不能为空',
            'length' => '标题太长',
        ],
        'cid' => [
            'intVal' => '必须为数字',
            'notEmpty' => '不能为空',
        ],
        'content' => [
            'notEmpty' => '不能为空',
        ]
    ];
}
