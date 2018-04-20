<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Log;

class LogController extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index($request, $response)
    {
        $query = Log::query();
        $query->where('is_delete', 0);
        $keys = $request->getParam('keys');
        if (!empty($keys)) {
            $query->where('event', 'like', '%'.$keys.'%');
        }

        $date = $request->getParam('date');
        if (!empty($date)) {
            $query->where('created_at', 'like', $date.'%');
        }
        $total = $query->count();
        $page = $request->getParam('page');
        if ($page == 0) {
            $page == 1;
        }
        $pagesize = 10;
        $startid = ($page - 1) * $pagesize;
        $list = $query->orderBy('id', 'desc')->skip($startid)->take($pagesize)->get(['id', 'username', 'logip', 'event', 'created_at']);
        $res['list'] = $list;
        $res['total'] = $total;
        return $response->withJson($res);
    }


    // 新增日志
    static public function addLog($str)
    {
        $log = new Log;
        $log->username = $_SESSION['admin_auth']['username'];
        $log->user_id = $_SESSION['admin_auth']['userid'];
        $log->logip = getip();
        $log->event = $str;
        $log->created_at = date('Y-m-d H:i:s');
        $log->save();
    }

    // 删除日志
    public function delete($request, $response)
    {
        $res['result'] = 'failed';
        if ($request->isPost()) {
            $ids = $request->getParam('id');
            $idArr = explode(',', $ids);
            $i = 0;
            foreach ($idArr as $key => $val) {
                $rs = Log::where('id', $val)->update(['is_delete' => 1]);
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
