<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Member;
use Respect\Validation\Validator as v;
use App\Validate\Member as valid;
use App\Controllers\LogController as Log;

class MemberController extends Controller
{
    // 会员列表
    public function index($request, $response)
    {
        $where = [];
        $where['is_delete'] = 0;
        $date = $request->getParam('date');
        if (!empty($date)) {
            $where[] = ['created_at', 'like', $date.'%'];
        }

        $keys = $request->getParam('keys');
        if (!empty($keys)) {
            $where[] = ['username', 'like', '%'.$keys.'%'];
        }

        $page = $request->getParam('page');
        if ($page == 0) {
            $page == 1;
        }
        $pagesize = 15;
        $startid = ($page - 1) * $pagesize;
        $list = Member::where($where)->orderBy('id', 'desc')->skip($startid)->take($pagesize)->get(['id', 'username', 'nickname', 'email', 'level', 'status', 'created_at']);
        return $response->withJson($list);
    }

    // 获取会员详情
    public function detail($request, $response, $arg)
    {
        $res['result'] = 'failed';
        if (isset($arg['id']) && $arg['id'] > 0) {
            $rs = Member::where('is_delete', 0)->find($arg['id']);
            if ($rs) {
                $res['data'] = $rs;
                $res['result'] = 'success';
            } else {
                $res['msg'] = '没有数据';
            }
            
        } else {
            $res['msg'] = '出错了！';
        }
        return $response->withJson($res);
    }

    // 修改会员
    public function update($request, $response)
    {
        $res['result'] = 'failed';
        if ($request->isPost()) {
            $rules = [
                'nickname' => v::stringType()->notEmpty()->length(3, 20),
            ];

            $valid = new valid;
            $validMsg = $valid->valid($request, $rules);
            if (!empty($validMsg)) {
                $res['msg'] = $validMsg;
                return $response->withJson($res);
            }
            
            $id = $request->getParam('id');
            if ($id > 0) {
                // 修改
                $post = $request->getParsedBody();
                $mem = new Member;
                $rs = $mem->where('id', $id)->update($post);
                if ($rs) {
                    Log::addLog('修改会员id:'.$id.'【'.$post['nickname'].'】');
                    $res['result'] = 'success';
                } else {
                    $res['msg'] = '修改失败';
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
                $rs = Member::where('id', $val)->update(['is_delete' => 1]);
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
}
