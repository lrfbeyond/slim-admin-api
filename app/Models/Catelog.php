<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catelog extends Model
{
    protected $table = 'catelog';
    public $timestamps = false;

    public function getCate()
    {
        $cate = $this->where('parentID',0)->where('is_delete', 0)->get(['id', 'title']);
        return $cate;
    }
}
