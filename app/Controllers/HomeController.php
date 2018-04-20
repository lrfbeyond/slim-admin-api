<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Article;
use App\Models\Member;
use App\Models\Comment;
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

    public function getTotals($request, Response $response)
    {
        $totalArticles = Article::where('is_delete', 0)->count();
        $totalComments = Comment::where('is_delete', 0)->count();
        $totalMembers = Member::where('is_delete', 0)->count();
        $res = [
            'totalArticles' => $totalArticles,
            'totalComments' => $totalComments,
            'totalMembers' => $totalMembers
        ];

        return $response->withJson($res);
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

    // 整站折线图统计-最近30天
    public function getLineData($request, $response)
    {
        $res['result'] = 'failed';
        //try {
            $dataArticle = [];
            $dataMember = [];
            $dataComment = [];
            for ($i = 29; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime('-'.$i.' days'));
                $dataArticle[] = Article::where('is_delete', 0)->where('created_at', 'like', $date.'%')->count();
                $dataMember[] = Member::where('is_delete', 0)->where('created_at', 'like', $date.'%')->count();
                $dataComment[] = Comment::where('is_delete', 0)->where('created_at', 'like', $date.'%')->count();
                
            }
            $dates = [];
            for ($i = 29; $i >= 0; $i--) {
                $dates[] = date('m月d日', strtotime('-'.$i.' days'));
            }

            $res = [
                'result' => 'success',
                'date' => $dates,
                'dataArticle' => $dataArticle,
                'dataMember' => $dataMember,
                'dataComment' => $dataComment
            ];
        // } catch (\Exception $e) {
        //     $res['msg'] = '出错了';
        // }
        return $response->withJson($res);
    }

    public function test2($req, $res)
    {
        $where['is_delete'] = 0;
        $date = $req->getParam('date');
        // if (!empty($date)) {
        //     $where[] = ['created_at', 'like', $date.'%'];
        // }

        $keys = $req->getParam('keys');
        // if (!empty($keys)) {
        //     $where[] = ['title', 'like', '%'.$keys.'%'];
        // }
        
        $query = Article::query();
        // if (!empty($keys)) {
        //     $query->where('title', 'like', '%'.$keys.'%');
        // }
        if (!empty($date)) {
            $query->where('created_at', 'like', $date.'%');
        }
        $query->when(!empty($keys), function ($q) use ($keys) {
            return $q->where('title', 'like', '%'.$keys.'%');
        });
        $query->where('id', '>=', 100)->orWhere('id', '<=', 300);
        // $query->where(function ($q) {
        //     return $q->where('id', '>=', 100);
        // })->orWhere(function ($q) {
        //     return $q->where('id', '<=', 300);
        // });
        $rs = $query->take(10)->orderBy('id', 'desc')->get(['id','title','created_at']);
        return $res->withJson($rs);
        exit;

        // $query->when(request('filter_by') == 'likes', function ($q) {
        //     return $q->where('likes', '>', request('likes_amount', 0));
        // });
        // $query->when(request('filter_by') == 'date', function ($q) {
        //     return $q->orderBy('created_at', request('ordering_rule', 'desc'));
        // });

        // 查询所有
        $rs = Article::where($where)->take(10)->orderBy('id', 'desc')->get(['id','title','created_at']);
        foreach ($rs as $k => $v) {
            //echo $v['title'] . "<br/>";
          //  echo $v->title.'<br/>';
        }
        return $res->withJson($rs);
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
