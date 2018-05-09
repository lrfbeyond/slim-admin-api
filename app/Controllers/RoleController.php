<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Role;
use Respect\Validation\Validator as v;
use App\Validate\Role as valid;
use App\Controllers\LogController as Log;
use Illuminate\Database\Capsule\Manager as DB;

class RoleController extends Controller
{
    // 列表
    public function index($request, $response)
    {
        $res['result'] = 'failed';

        $query = Role::query();
        $query->where('is_delete', 0);

        $keys = $request->getParam('keys');
        if (!empty($keys)) {
            $query->where('title', 'like', '%'.$keys.'%');
        }

        $total = $query->count();

        $page = $request->getParam('page');
        if ($page == 0) {
            $page == 1;
        }
        $pagesize = 10;
        $startid = ($page - 1) * $pagesize;
        $list = $query->skip($startid)->take($pagesize)->get(['id', 'title', 'intro']);
        $res['result'] = 'success';
        $res['list'] = $list;
        $res['total'] = $total;
        return $response->withJson($res);
    }

    // 获取详情
    public function detail($request, $response, $arg)
    {
        $res['result'] = 'failed';
        if (isset($arg['id']) && $arg['id'] > 0) {
            $rs = Role::where('is_delete', 0)->select('id','title','intro','permission')->find($arg['id']);
            if ($rs) {
                $permission = explode(',', $rs['permission']);
                $checked = [];
                foreach ($permission as $key => $val) {
                    $checked[] = (int)$val;
                }

                $rs['checked'] = $checked;
                unset($rs['permission']);
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

    // 修改/新增
    public function update($request, $response)
    {
        $res['result'] = 'failed';

        if ($request->isPost()) {
            $rules = [
                'title' => v::stringType()->notEmpty()->length(null, 64)
            ];

            $valid = new valid;
            $validMsg = $valid->valid($request, $rules);
            if (!empty($validMsg)) {
                $res['msg'] = $validMsg;
                return $response->withJson($res);
            }

            $post = $request->getParsedBody();
            $permission = implode(',', $post['checked']);
            
            $id = $request->getParam('id');
            if ($id > 0) {
                // 修改
                $mod = new Role;
                $post['permission'] = $permission;
                unset($post['checked']);
                $rs = $mod->where('id', $id)->update($post);
                if ($rs) {
                    Log::addLog('修改角色id:'.$id.'【'.$post['title'].'】');
                    $res['result'] = 'success';
                } else {
                    $res['msg'] = '修改失败';
                }
            } else {
                // 新增
                $mod = new Role;
                $mod->title = $post['title'];
                $mod->intro = $post['intro'];
                $mod->permission = $permission;
                $mod->save();
                $aid = $mod->id;
                if ($aid > 0) {
                    Log::addLog('新增角色id:'.$aid.'【'.$post['title'].'】');
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
            $ids = $request->getParam('id');
            $idArr = explode(',', $ids);
            $i = 0;
            foreach ($idArr as $key => $val) {
                $i++;
                if ($val == '1') {
                    continue;
                } else {
                    $rs = Role::where('id', $val)->update(['is_delete' => 1]);
                }
            }
            if ($i == count($idArr)) {
                $res['result'] = 'success';
            } else {
                $res['msg'] = '删除失败';
            }
        } else {
            $res['msg'] = '非法提交';
        }
        return $response->withJson($res);
    }

    // 获取角色权限列表
    public function getPermission($request, $response)
    {
        $res['result'] = 'failed';
        if (isset($arg['id']) && $arg['id'] > 0) {
            $rs = DB::table('permission')->where('is_delete', 0)->find($arg['id']);
            if ($rs) {
                $res['row'] = $rs;
                $res['result'] = 'success';
            } else {
                $res['msg'] = '没有数据';
            }
            
        } else {
            $list = DB::table('permission')->where('is_delete', 0)->orderBy('sort')->get(['id', 'title', 'pid']);
            $data =  json_decode(json_encode($list), true);
            $res['result'] = 'success';
            $res['row'] = $this->getTree($data);
            $res['checked'] = [6, 13];
        }
        return $response->withJson($res);
    }

    private function getTree($data, $pid = 0, $deep = 0)
    {
        $tree = [];
        
        foreach($data as $k => $v) {
            if ($pid == 0) {
                $v['checked'] = [];
            }
            if($v['pid'] == $pid) {
                $v['deep'] = $deep;
                unset($data[$k]);
                $v['child'] = $this->getTree($data, $v['id'], $deep+1);
                $tree[] = $v;
            }
        }
        return $tree;
    }
}
