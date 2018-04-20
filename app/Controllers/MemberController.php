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
        $query = Member::query();
        $query->where('is_delete', 0);
        $date = $request->getParam('date');
        if (!empty($date)) {
            $query->where('created_at', 'like', $date.'%');
        }

        $keys = $request->getParam('keys');
        if (!empty($keys)) {
            $query->where('username', 'like', '%'.$keys.'%');
        }
        $total = $query->count();

        $page = $request->getParam('page');
        if ($page == 0) {
            $page == 1;
        }
        $pagesize = 10;
        $startid = ($page - 1) * $pagesize;
        $list = $query->orderBy('id', 'desc')->skip($startid)->take($pagesize)->get(['id', 'username', 'nickname', 'email', 'login_oauth', 'status', 'created_at']);
        foreach ($list as & $val) {
            $status = $val['status'];
            if ($status == 1) {
                $val['stat'] = '禁言';
            } elseif ($status == 2) {
                $val['stat'] = '禁用';
            } else {
                $val['stat'] = '正常';
            }
            $oauth = $val['login_oauth'];
            switch ($oauth) {
                case '0':
                    $from = '本站';
                    break;
                case '1':
                    $from = 'QQ';
                    break;
                case '2':
                    $from = '微信';
                    break;
                case '3':
                    $from = '微博';
                    break;
                case '4':
                    $from = 'Github';
                    break;
                default:
                    $from = '本站';
                    break;
            }
            $val['from'] = $from;
        }
        $res['list'] = $list;
        $res['total'] = $total;
        return $response->withJson($res);
    }

    // 获取会员详情
    public function detail($request, $response, $arg)
    {
        $res['result'] = 'failed';
        if (isset($arg['id']) && $arg['id'] > 0) {
            $rs = Member::where('is_delete', 0)->where('id', $arg['id'])->first(['id','username','nickname','sex','email','photo','login_oauth','oauth_id','regip','level','status','created_at','updated_at']);
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
