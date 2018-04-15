<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Comment;
use App\Models\Article;
use App\Models\Member;
use Respect\Validation\Validator as v;
use App\Validate\Member as valid;
use App\Controllers\LogController as Log;

class CommentController extends Controller
{
    // 会员列表
    public function index($request, $response)
    {
        $where = [];
        $where['is_delete'] = 0;
        $where['parents'] = 0;
        $date = $request->getParam('date');
        if (!empty($date)) {
            $where[] = ['created_at', 'like', $date.'%'];
        }

        $keys = $request->getParam('keys');
        if (!empty($keys)) {
            $where[] = ['message', 'like', '%'.$keys.'%'];
        }
        $total = Comment::where($where)->count();

        $page = $request->getParam('page');
        if ($page == 0) {
            $page == 1;
        }
        $pagesize = 10;
        $startid = ($page - 1) * $pagesize;
        $list = Comment::where($where)->orderBy('id', 'desc')->skip($startid)->take($pagesize)->get(['id', 'article_id', 'author_id', 'ip', 'message', 'is_reply', 'created_at']);
        foreach ($list as & $val) {
            $article_id = $val['article_id'];
            if ($article_id == 0) {
                $val['article_title'] = '用户留言';
            } else {
                $val['article_title'] = Article::where('id', $article_id)->value('title');
            }

            $val['author_name'] = Member::where('id', $val['author_id'])->value('nickname');
            
        }
        $res['list'] = $list;
        $res['total'] = $total;
        return $response->withJson($res);
    }

    // 获取评论详情
    public function detail($request, $response, $arg)
    {
        $res['result'] = 'failed';
        if (isset($arg['id']) && $arg['id'] > 0) {
            $rs = Comment::where('is_delete', 0)->where('id', $arg['id'])->first(['id','author_id','article_id','ip','is_reply','message','created_at','updated_at']);
            if ($rs) {
                $article_id = $rs['article_id'];
                if ($article_id == 0) {
                    $rs['article_title'] = '用户留言';
                } else {
                    $rs['article_title'] = Article::where('id', $article_id)->value('title');
                }
                $rs['author_name'] = Member::where('id', $rs['author_id'])->value('nickname');
                if ($rs['is_reply'] == 1) {
                    $rs['reply'] = Comment::where('parents', $rs['id'])->value('message');
                }
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

    // 回复评论
    public function reply($request, $response)
    {
        $res['result'] = 'failed';
        if ($request->isPost()) {
            $rules = [
                'reply' => v::stringType()->notEmpty()->length(1, 200),
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
                $comment = new Comment;
                $comment->article_id = $post['aid'];
                $comment->author_id = 1;
                $comment->author_name = '月光光';
                $comment->parents = $post['id'];
                $comment->message = $post['reply'];
                $comment->ip = getip();
                $aid = $comment->save();
                if ($aid > 0) {
                    $com = Comment::find($post['id']);
                    $com->is_reply = 1;
                    $com->save();
                    //Comment::where('id', $post['id'])->update(['is_reply', 1]);
                    Log::addLog('回复评论id:'.$id.'【'.cutStr($post['message'], 20).'】');
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
                $rs = Comment::where('id', $val)->update(['is_delete' => 1]);
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
