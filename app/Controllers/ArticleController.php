<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Article;
use App\Models\Catelog;

class ArticleController extends Controller
{
    public function index($request, $response)
    {
        $res['result'] = 'failed';

        $where = [];
        $where['is_delete'] = 0;
        $cate = $request->getParam('cate');
        if (!empty($cate)) {
            $where['cid'] = $cate;
        }

        $keys = $request->getParam('keys');
        if (!empty($keys)) {
            $where[] = ['title', 'like', '%'.$keys.'%'];
        }

        $page = $request->getParam('page');
        if ($page == 0) {
            $page == 1;
        }
        $pagesize = 2;
        $startid = ($page - 1) * $pagesize;
        $list = Article::where($where)->orderBy('id', 'desc')->skip($startid)->take($pagesize)->get(['id', 'title', 'cid', 'hits', 'downs', 'mark', 'created_at']);
        foreach ($list as $key => & $val) {
            $val['cate'] = Catelog::where('id', $val['cid'])->value('title');
        }
        $res['result'] = 'success';
        $res['data'] = $list;
        //$res['data'] = $response->withJson($list);
        return $response->withJson($res);
    }

    // 获取文章详情
    public function detail($request, $response, $arg)
    {
        $res['result'] = 'failed';
        if (isset($arg['id']) && $arg['id'] > 0) {
            $rs = Article::where('is_delete', 0)->find($arg['id']);
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

    // 修改文章/新增
    public function update($request, $response)
    {
        $res['result'] = 'failed';
        if ($request->isPost()) {
            $id = $request->getParam('id');
            if ($id > 0) {
                // 修改
                $post = $request->getParsedBody();
                $art = new Article;
                $rs = $art->where('id', $id)->update($post);
                if ($rs) {
                    $res['result'] = 'success';
                } else {
                    $res['msg'] = '修改失败';
                }
            } else {
                // 新增
                $post = $request->getParsedBody();
                $art = new Article;
                $art->title = $post['title'];
                $art->content = $post['content'];
                $art->save();
                $aid = $art->id;
                if ($aid > 0) {
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
                $rs = Article::where('id', $val)->update(['is_delete' => 1]);
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
