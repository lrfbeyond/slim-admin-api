<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catelog extends Model
{
    protected $table = 'catelog';

    public function getCate()
    {
        $cate = $this->where('parentID',0)->where('is_delete', 0)->get(['id', 'title']);
        return $cate;
    }

    public function getCateTitle()
    {
        $cate = $this->where('parentID',0)->where('is_delete', 0)->get(['title']);
        return $cate;
    }
}
