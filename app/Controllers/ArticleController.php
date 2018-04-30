<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Article;
use App\Models\Catelog;
use App\Models\Tag;
use Respect\Validation\Validator as v;
use App\Validate\Article as valid;
use App\Controllers\LogController as Log;
use Upload\File;

class ArticleController extends Controller
{
    public function index($request, $response)
    {
        $res['result'] = 'failed';

        $query = Article::query();
        $query->where('is_delete', 0);
        $cate = $request->getParam('cate');
        if (!empty($cate)) {
            $query->where('cid', $cate);
        }

        $date = $request->getParam('date');
        if (!empty($date)) {
            $query->where('created_at', 'like', $date.'%');
        }

        $keys = $request->getParam('keys');
        if (!empty($keys)) {
            $query->where('title', 'like', '%'.$keys.'%');
        }

        $total = $query->count();

        $page = $request->getParam('page');
        if ($page == 0) {
            $page == 1;
        }
        $pagesize = 10;
        $startid = ($page - 1) * $pagesize;
        $list = $query->orderBy('id', 'desc')->skip($startid)->take($pagesize)->get(['id', 'title', 'cid', 'hits', 'mark', 'created_at']);
        foreach ($list as $key => & $val) {
            $val['cate'] = Catelog::where('id', $val['cid'])->value('title');
        }
        $res['result'] = 'success';
        $res['list'] = $list;
        $res['total'] = $total;
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
                $rs['keywords'] = explode(",", $rs['keywords']);
                $rs['isorig'] = $rs['isorig'] == 1 ? true : false;
                $rs['ishot'] = $rs['ishot'] == 1 ? true : false;
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

    // 修改文章/新增
    public function update($request, $response)
    {
        $res['result'] = 'failed';

        if ($request->isPost()) {
            $rules = [
                'title' => v::stringType()->notEmpty()->length(null, 64),
                'cid' => v::intVal()->notEmpty(),
                'content' => v::notEmpty(),
            ];

            $valid = new valid;
            $validMsg = $valid->valid($request, $rules);
            if (!empty($validMsg)) {
                $res['msg'] = $validMsg;
                return $response->withJson($res);
            }

            $post = $request->getParsedBody();

            if ($post['keywords']) {
                $post['keywords'] = implode(',', $post['keywords']);
            }
            
            $id = $request->getParam('id');
            if ($id > 0) {
                // 修改
                $art = new Article;
                $rs = $art->where('id', $id)->update($post);
                if ($rs) {
                    Log::addLog('修改文章id:'.$id.'【'.$post['title'].'】');
                    $res['result'] = 'success';
                } else {
                    $res['msg'] = '修改失败';
                }
            } else {
                // 新增
                if (empty($post['intro'])) {
                    $post['intro'] = cutStr(strip_tags($post['content']), 100);
                }
                
                $art = new Article;
                $art->cid = $post['cid'];
                $art->title = $post['title'];
                $art->keywords = $post['keywords'];
                $art->intro = $post['intro'];
                $art->author = $post['author'];
                $art->source = $post['source'];
                $art->pic = $post['pic'];
                $art->ishot = $post['ishot'];
                $art->isorig = $post['isorig'];
                $art->content = $post['content'];
                $art->save();
                $aid = $art->id;
                if ($aid > 0) {
                    Log::addLog('新增文章id:'.$aid.'【'.$post['title'].'】');
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

    public function upload($request, $response)
    {
        $res['result'] = 'failed';
        $upfile = $_FILES["file"];
        if ($upfile) {
            $file = new File($upfile);

            $file->validate = [
                'size' => 500*1024,
                'ext' => 'jpg,png,gif'
            ];

            $upload_dir =  '..'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'uploads';

            $info = $file->upload($upload_dir);
            $res['result'] = 'success';
            $savename = str_replace('\\','/',$info['savename']);
            $res['savename'] = $savename;
        } else {
            $res['msg'] = '上传失败！';
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

    public function getCate($request, $response)
    {
        $res = Catelog::where('parentID',0)->where('is_delete', 0)->get(['id', 'title']);
        return $response->withJson($res);
    }

    public function getTags($request, $response)
    {
        $res = Tag::where('is_delete', 0)->get(['id', 'ename', 'tagname']);
        return $response->withJson($res);
    }

    public function test()
    {
        $number = 123;
        $rs = v::numeric()->validate($number); 
        print_r($rs);
    }
}
