<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Article;

class HomeController extends Controller
{
    public function index($req, $res)
    { 
        $this->logger->info("haha index");
        $rs = Article::find(1);
        return $rs->title;
    }

    public function test($req, $res)
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
}
