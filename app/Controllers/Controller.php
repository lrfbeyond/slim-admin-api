<?php 
namespace App\Controllers;

use Illuminate\Database\Capsule\Manager as DB;

class Controller
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }

    public function _initialize()
    {
        header("Access-Control-Allow-Origin: *");
        //header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-token");
    }

    // 获取树状结构
    public function getCateTree()
    {
        $list = DB::table('catelog')->where('is_delete', 0)->get(['id', 'pid', 'title', 'sort']);
        $object =  json_decode(json_encode($list), true);
        return $this->myTree($object);
    }

    static public $treeList = [];

    static public function myTree(&$data, $pid = 0, $count = 1)
    {
        foreach ($data as $key => $val){
            if($val['pid'] == $pid){
                $val['level'] = $count;
                $val['label'] = str_repeat('&nbsp;&nbsp;', $count).'├─ '.$val['title'];
                //$val['rawTtitle'] = 
                self::$treeList [] = $val;
                //unset($data[$key]);
                self::myTree($data, $val['id'], $count + 1);
            }
        }
        return self::$treeList;
    }
}
