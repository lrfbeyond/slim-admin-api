<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Log;

class LogController extends Controller
{
    public function index($request, $response)
    {
        $res['result'] = 'failed';

        $where = [];
        $where['is_delete'] = 0;
        $uname = $request->getParam('uname');
        if (!empty($uname)) {
            $where['username'] = $uname;
        }

        $date = $request->getParam('date');
        if (!empty($date)) {
            $where[] = ['created_at', 'like', $date.'%'];
        }

        $page = $request->getParam('page');
        if ($page == 0) {
            $page == 1;
        }
        $pagesize = 20;
        $startid = ($page - 1) * $pagesize;
        $list = Log::where($where)->orderBy('id', 'desc')->skip($startid)->take($pagesize)->get(['id', 'username', 'logip', 'event', 'created_at']);
        // foreach ($list as $key => & $val) {
        //     $val['cate'] = Catelog::where('id', $val['cid'])->value('title');
        // }
        $res['result'] = 'success';
        $res['data'] = $list;
        //$res['data'] = $response->withJson($list);
        return $response->withJson($res);
    }


    // 新增日志
    static public function addLog($str)
    {
        $log = new Log;
        $log->username = $_SESSION['admin_auth']['username'];
        $log->logip = getip();
        $log->event = $str;
        $log->created_at = date('Y-m-d H:i:s');
        $log->save();
    }
}
