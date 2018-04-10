<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Article;
use Respect\Validation\Validator as v;
//use Respect\Validation\Exceptions\NestedValidationException;

use App\Validate\Article as valid;

class HomeController extends Controller
{
    public function index($req, $res)
    { 
        $this->logger->info("haha index");
        $rs = Article::find(502);
        return $rs->title;
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
        // $number = '123x';
        // $rs = v::numeric()->validate($number); 
        //print_r($rs);
        $rules = [
            'email' => v::noWhitespace()->notEmpty()->email(),
            'name' => v::noWhitespace()->notEmpty()->alpha(),
            'password' => v::noWhitespace()->notEmpty(),
        ];

        $valid = new valid;

        $rs = $valid->valid($request, $rules);
        print_r($rs);

        // $usernameValidator = v::alnum()->noWhitespace()->length(3, 15);
        // $rs = $usernameValidator->validate('2ae w'); // true
        // echo $rs;

        // $user = new Article;
        // $user->name = 'Alexandre';
        // $user->birthdate = '1987-07-01';

        // $userValidator = v::attribute('name', v::stringType()->length(1,32))
        //           ->attribute('birthdate', v::date()->age(18));

        // $rs = $userValidator->validate($user); // true
        // echo $rs;

        // try {
        //     $usernameValidator = v::alnum()->noWhitespace()->length(3, 15);
        //     $usernameValidator->assert('re');
        // } catch(NestedValidationException $exception) {
        //    //echo $exception->getFullMessage();
        //    //print_r($exception->getMessages());
        //    //print_r($exception->findMessages(['alnum', 'noWhitespace', 'length']));

        //    $errors = $exception->findMessages([
        //         'alnum' => '{{name}} must contain only letters and digits',
        //         'length' => 'must not have more than 15 chars',
        //         'noWhitespace' => '{{name}} cannot contain spaces'
        //     ]);
        //    print_r($errors);
        //}

        // $rules = [
        //     'email' => v::noWhitespace()->notEmpty()->email(),
        //     'name' => v::noWhitespace()->notEmpty()->alpha(),
        //     'password' => v::noWhitespace()->notEmpty(),
        // ];
        // $messages = [
        //     'email' => [
        //         'noWhitespace' => '邮箱不能有空格',
        //         'notEmpty' => '邮箱不能为空',
        //         'email' => '邮箱格式不对',
        //     ],
        //     'name' => [
        //         'noWhitespace' => '不能有空格',
        //         'notEmpty' => '不能为空',
        //         'alpha' => '必须字母',
        //     ],
        //     'password' => [
        //         'noWhitespace' => '不能有空格',
        //         'notEmpty' => '不能为空',
        //     ]
        // ];
        
        // $errors = [];
        // $errorMsg = '';
        // foreach ($rules as $field => $rule) {
        //     try {
        //         $rule->assert($request->getParam($field));
        //     } catch (NestedValidationException $e) {
        //         $errors[$field] = $e->findMessages(
        //             $messages[$field]
        //         );
        //     }
        //     foreach ($errors[$field] as $key => $val) {
        //         if (!empty($val)) {
        //             $errorMsg = $val;
        //             break;
        //         }
        //     }
        //     if (!empty($errorMsg)) {
        //         break;
        //     }
        // }
        
        // print_r($errorMsg);

    }
}
