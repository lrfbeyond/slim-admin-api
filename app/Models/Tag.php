<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tag';

    // 关闭自动更新时间
    public $timestamps = false; 
}
