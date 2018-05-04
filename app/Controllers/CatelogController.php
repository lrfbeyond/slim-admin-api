<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Catelog;
use Respect\Validation\Validator as v;
use App\Validate\Catelog as valid;
use Illuminate\Database\Capsule\Manager as DB;
use App\Controllers\LogController as Log;

class CatelogController extends Controller
{
    public function index($request, $response)
    {
        $depList = $this->getDepartTree();
        return $response->withJson($depList);
    }

    // 获取分类信息
    public function detail($request, $response, $arg)
    {
        $res['result'] = 'failed';
        if (isset($arg['id']) && $arg['id'] > 0) {
            $rs = Catelog::where('is_delete', 0)->find($arg['id']);
            if ($rs) {
                $res['row'] = $rs;
                $res['result'] = 'success';
            } else {
                $res['msg'] = '没有数据';
            }
            
        } else {
            $res['msg'] = '出错了！';
        }
        return $response->withJson($res);
    }

    // 修改分类/新增
    public function update($request, $response)
    {
        $res['result'] = 'failed';

        if ($request->isPost()) {
            $rules = [
                'title' => v::stringType()->notEmpty()->length(null, 64),
                'pid' => v::intVal()->notEmpty(),
            ];

            $valid = new valid;
            $validMsg = $valid->valid($request, $rules);
            if (!empty($validMsg)) {
                $res['msg'] = $validMsg;
                return $response->withJson($res);
            }

            $post = $request->getParsedBody();
            
            $id = $request->getParam('id');
            if ($id > 0) {
                // 修改
                $mod = new Catelog;
                $rs = $mod->where('id', $id)->update($post);
                if ($rs) {
                    Log::addLog('修改分类id:'.$id.'【'.$post['title'].'】');
                    $res['result'] = 'success';
                } else {
                    $res['msg'] = '修改失败';
                }
            } else {
                // 新增
                $mod = new Catelog;
                $mod->pid = $post['pid'];
                $mod->title = $post['title'];
                $mod->sort = $post['sort'];
                $mod->save();
                $aid = $mod->id;
                if ($aid > 0) {
                    Log::addLog('新增分类id:'.$aid.'【'.$post['title'].'】');
                    $res['result'] = 'success';
                } else {
                    $res['msg'] = '新增失败';
                }
            }
        } else {
            $res['msg'] = '非法提交';
        }
        return $response->withJson($res);
    }

    // 删除记录-假删除
    public function delete($request, $response)
    {
        $res['result'] = 'failed';
        if ($request->isPost()) {
            $id = $request->getParam('id');
            $rs = Catelog::where('id', $id)->update(['is_delete' => 1]);
            if ($rs) {
                //查看此分类下是否有文章
                $hasArt = Article::where('cid', $id)->where('is_delete', 0)->count();
                if ($hasArt > 0) {
                    $res['msg'] = '请先删除该分类下的所有文章！';
                    return $response->withJson($res);
                }

                //查看此分类下是否有子分类
                $hasChild = Catelog::where('pid', $id)->where('is_delete', 0)->count();
                if ($hasChild > 0) {
                    $res['msg'] = '请先删除该分类下的子分类！';
                    return $response->withJson($res);
                }
                $res['result'] = 'success';
                Log::addLog('删除分类id:'.$ids);
            } else {
                $res['msg'] = '删除失败';
            }
        } else {
            $res['msg'] = '非法提交';
        }
        return $response->withJson($res);
    }

    public function getCateTree($request, $response)
    {
        $list = DB::table('catelog')->where('is_delete', 0)->get(['id', 'pid', 'title', 'sort']);
        $data =  json_decode(json_encode($list), true);
        $tree = $this->getTree($data);
        return $response->withJson($tree);
    }

    // 获取树状结构
    private function getDepartTree()
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
                unset($data[$key]);
                self::myTree($data, $val['id'], $count + 1);
            }
        }
        return self::$treeList;
    }

    private function getTree($data, $pid = 0, $deep = 0)
    {
        $tree = [];
        
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

}

