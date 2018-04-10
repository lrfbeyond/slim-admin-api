<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'log';

    // 关闭自动更新时间
    public $timestamps = false; 
}
