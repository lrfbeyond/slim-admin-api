<?php 
namespace App\Validate;

use App\Validate\Validator;

class Comment extends Validator
{
    protected $messages = [
        'reply' => [
            'notEmpty' => '回复内容不能为空',
            'length' => '回复内容太长',
        ],
        'message' => [
            'length' => '评论内容太长',
            'notEmpty' => '评论内容不能为空',
        ],
    ];
}
