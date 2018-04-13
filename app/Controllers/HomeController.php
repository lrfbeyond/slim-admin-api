<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Article;
use App\Models\Log;
use App\Models\Catelog;

class HomeController extends Controller
{
    public function index($req, $res)
    { 
        $this->logger->info("haha index");
        $rs = Article::find(502);
        return $rs->title;
    }

    // 获取最新操作日志
    public function getOptLog($request, $response)
    {
        $res['result'] = 'failed';
        $user_auth = $_SESSION['admin_auth'];
        //print_r($user_auth);
        if (!empty($user_auth)) {
            $list = Log::where('user_id', $user_auth['userid'])->orderBy('id', 'desc')->take(8)->get(['id','event','created_at']);
            $res['list'] = $list;
            $res['result'] = 'success';
        } 
        return $response->withJson($res);
    }

    // 饼状图-资讯各分类总数
    public function getPieData($request, $response)
    {
        $res['result'] = 'failed';
        try {
            $catelog = new Catelog;
            $cate = $catelog->getCate();
            $data = [];
            foreach ($cate as $key => $val) {
                $data[] = [
                    'name' => $val['title'],
                    'value' => Article::where('cid', $val['id'])->where('is_delete', 0)->count()
                ];
            }
            $res['data'] = $data;
            $res['result'] = 'success';
        } catch (\Exception $e) {
            $res['msg'] = '出错了';
        }
        return $response->withJson($res);
    }

    // 柱状图-资讯统计-最近30天
    public function getBarData($request, $response)
    {
        $res['result'] = 'failed';
        //try {
            $catelog = new Catelog;
            $cate = $catelog->getCate();
            $data = [];
            foreach ($cate as $key => $val) {
                for ($i = 29; $i >= 0; $i--) {
                    $date = date('Y-m-d', strtotime('-'.$i.' days'));
                    $data[$key][] = Article::where('cid', $val['id'])->where('created_at', 'like', $date.'%')->where('is_delete', 0)->count();
                    
                }
            }
            $dates = [];
            for ($i = 29; $i >= 0; $i--) {
                $dates[] = date('m月d日', strtotime('-'.$i.' days'));
            }
            $res['date'] = $dates;
            $res['data'] = $data;
            $res['result'] = 'success';
        // } catch (\Exception $e) {
        //     $res['msg'] = '出错了';
        // }
        return $response->withJson($res);
    }

    public function getCate()
    {
        $catelog = new Catelog;
        $cate = $catelog->getCateTitle();
        return $response->withJson($res);
    }

    public function test2($req, $res)
    {
        // 查询所有
        $rs = Article::where('id', '>=', 3)->orderBy('id', 'desc')->get(['id','title']);
        foreach ($rs as $k => $v) {
            //echo $v['title'] . "<br/>";
          //  echo $v->title.'<br/>';
        }
        
        //查询1个
        $rs = Article::find(1);
        //echo $rs->title;

        //查询多个
        $rs = Article::find([1,2,3], ['id', 'title', 'created_at']);
        foreach ($rs as $k => $v) {
            //echo $v->title.'<br/>';
        }
        // return $res->withJson($rs);
        //  exit;

         

        //获取第一个
       //$rs = Article::where('id', '>', '2')->first();
        $rs = Article::where('id', '>', '2')->first(['id', 'title']);
        return $res->withJson($rs);
        //echo $rs->title;
        //聚合
        $count = Article::where('id', '>', 2)->count();
        //echo $count;

        //新增
        // $art = new Article;
        // $art->title = 'abcw我的歌';
        // $rs = $art->save();
        // echo $art->id;
        // print_r($rs);

        //update
        // $art = Article::find(1);
        // $art->title = '中国人民';
        // $rs = $art->save();
        // print_r($rs);



    }

    public function test($request, $response)
    {
        //
    }
}
