<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Admin;
use App\Models\Role;
use Respect\Validation\Validator as v;
use App\Validate\Admin as valid;
use App\Controllers\LogController as Log;

class AdminController extends Controller
{
    // 管理员列表
    public function index($request, $response)
    {
        $res['result'] = 'failed';

        $query = Admin::query();
        $query->where('is_delete', 0);

        $date = $request->getParam('date');
        if (!empty($date)) {
            $query->where('created_at', 'like', $date.'%');
        }

        $keys = $request->getParam('keys');
        if (!empty($keys)) {
            $query->where('admin_name', 'like', '%'.$keys.'%');
            $query->orWhere('realname', 'like', '%'.$keys.'%');
        }

        $total = $query->count();

        $page = $request->getParam('page');
        if ($page == 0) {
            $page == 1;
        }
        $pagesize = 10;
        $startid = ($page - 1) * $pagesize;
        $list = $query->orderBy('id', 'desc')->skip($startid)->take($pagesize)->get(['id', 'admin_name', 'realname', 'role_id', 'login_counts', 'updated_at', 'created_at']);
        $role = new Role;
        foreach ($list as $key => & $val) {
            $val['role'] = $role->where('id', $val['role_id'])->value('title');
        }
        $res['result'] = 'success';
        $res['list'] = $list;
        $res['total'] = $total;
        $res['rolelist'] = $role->getList();
        //$res['data'] = $response->withJson($list);
        return $response->withJson($res);
    }

    // 获取详情
    public function detail($request, $response, $arg)
    {
        $res['result'] = 'failed';
        if (isset($arg['id']) && $arg['id'] > 0) {
            $rs = Admin::where('is_delete', 0)->find($arg['id']);
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

    // 修改/新增
    public function update($request, $response)
    {
        $res['result'] = 'failed';

        if ($request->isPost()) {
            $post = $request->getParsedBody();
            $admin_name = $post['admin_name'];
            $id = $request->getParam('id');
            if ($id > 0) {
                // 修改
                $rules = [
                    'admin_name' => v::stringType()->notEmpty()->length(6, 20),
                    'role_id' => v::intVal()->notEmpty(),
                ];
                $valid = new valid;
                $validMsg = $valid->valid($request, $rules);
                if (!empty($validMsg)) {
                    $res['msg'] = $validMsg;
                    return $response->withJson($res);
                }
                $mod = new Admin;
                // 是否已存在账号
                $hasExist = $mod::where('is_delete', 0)->where('id', '<>', $id)->where('admin_name', $admin_name)->count();
                if ($hasExist > 0) {
                    $res['msg'] = '该账号已存在';
                    return $response->withJson($res);
                }

                unset($post['password'], $post['password_confirm']);
                $rs = $mod->where('id', $id)->update($post);
                if ($rs) {
                    Log::addLog('修改管理员id:'.$id.'【'.$admin_name.'】');
                    $res['result'] = 'success';
                } else {
                    $res['msg'] = '修改失败';
                    $this->logger->info("修改管理员失败");
                }
            } else {
                // 新增
                $rules = [
                    'admin_name' => v::stringType()->notEmpty()->length(6, 20),
                    'password' => v::stringType()->notEmpty()->length(6, 16),
                    'role_id' => v::intVal()->notEmpty(),
                ];
                $valid = new valid;
                $validMsg = $valid->valid($request, $rules);
                if (!empty($validMsg)) {
                    $res['msg'] = $validMsg;
                    return $response->withJson($res);
                }
                if ($post['password'] !== $post['password_confirm']) {
                    $res['msg'] = '两次密码输入不一致';
                    return $response->withJson($res);
                }

                $passwordHash = password_hash(
                   $post['password'],
                   PASSWORD_DEFAULT,
                   ['cost' => 12]
                );
                $mod = new Admin;

                // 是否已存在账号
                $hasExist = $mod::where('is_delete', 0)->where('admin_name', $admin_name)->count();
                if ($hasExist > 0) {
                    $res['msg'] = '该账号已存在';
                    return $response->withJson($res);
                }

                $mod->role_id = $post['role_id'];
                $mod->password = $passwordHash;
                $mod->admin_name = $admin_name;
                $mod->realname = $post['realname'];
                $mod->save();
                $aid = $mod->id;
                if ($aid > 0) {
                    Log::addLog('新增管理员id:'.$aid.'【'.$admin_name.'】');
                    $res['result'] = 'success';
                } else {
                    $res['msg'] = '新增失败';
                    $this->logger->info("新增管理员失败");
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
                $rs = Admin::where('id', $val)->update(['is_delete' => 1]);
                if ($rs) {
                    $i++;
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

    // 重置密码666666
    public function resetPass($request, $response)
    {
        $res['result'] = 'failed';
        if ($request->isPost()) {
            $id = $request->getParam('id');
            $passwordHash = password_hash(
                   '666666',
                   PASSWORD_DEFAULT,
                   ['cost' => 12]
                );
            $rs = Admin::where('id', $id)->update(['password' => $passwordHash]);
            if ($rs) {
                $res['result'] = 'success';
            } else {
                $res['msg'] = '重置密码失败';
            }
        } else {
            $res['msg'] = '非法提交';
        }
        return $response->withJson($res);
    }
}
