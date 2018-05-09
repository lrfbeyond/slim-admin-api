<?php 
namespace App\Controllers\Home;

use App\Controllers\Controller;
use App\Models\Article;
use App\Models\Catelog;
use App\Models\Department;
//use Illuminate\Support\Facades\DB;
use Illuminate\Database\Capsule\Manager as DB;

class IndexController extends Controller
{
    public function index($request, $response)
    {

        $query = Article::query();
        $query->where('is_delete', 0);
        $page = $request->getParam('page');
        if ($page == 0) {
            $page == 1;
        }
        $pagesize = 10;
        $startid = ($page - 1) * $pagesize;
        $list = $query->orderBy('id', 'desc')->skip($startid)->take($pagesize)->get(['id', 'cid', 'title', 'created_at']);
        foreach ($list as $key => & $val) {
            $val['cate'] = Catelog::where('id', $val['cid'])->value('title');
        }

        $depList = $this->getDepartTree();
        echo json_encode($depList);

        return $this->view->render($response, 'test.html', [
            'name' => 'Grandhonor',
            'list' => $list,
            'dep' => $depList
        ]);
    }

    // 获取组织树状结构
    private function getDepartTree()
    {
        $list = DB::table('department')->where('is_delete', 0)->get(['id', 'pid', 'title']);
        //print_r($list);exit;
        $object =  json_decode(json_encode($list), true);
        return $this->myTree($object);
    }

    static public $treeList = array();

    static public function myTree(&$data, $pid = 0, $count = 1) {
        foreach ($data as $key => $value){
            if($value['pid'] == $pid){
                $value['level'] = $count;
                $value['title'] = str_repeat('&nbsp;&nbsp;', $count).'├─ '.$value['title'];
                self::$treeList [] = $value;
                unset($data[$key]);
                self::myTree($data, $value['id'], $count + 1);
            } 
        }
        return self::$treeList;
    }

    public function test($request, $response)
    {
        $items = DB::table('department')->where('is_delete', 0)->get(['id', 'pid', 'title']);
        $data =  json_decode(json_encode($items), true);
        $tree = $this->getTree($data);
        return $response->withJson($tree);
    }

    private function getTree($data, $pid = 0, $deep = 0)
    {
        $tree = '';
        
        foreach($data as $k => $v) {
            if($v['pid'] == $pid) {
                $v['label'] = $v['title'];
                $v['deep'] = $deep;
                unset($data[$k]);
                unset($v['title']);
                $v['children'] = $this->getTree($data, $v['id'], $deep+1);
                $tree[] = $v;
                
                
            }
        }
        return $tree;
    }

    public function utest($request, $response)
    {
        echo '123';
    }
}
