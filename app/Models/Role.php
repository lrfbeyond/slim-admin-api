<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'role';
    public $timestamps = false;

    public function getList()
    {
        $cate = $this->where('is_delete', 0)->get(['id', 'title']);
        return $cate;
    }
}
