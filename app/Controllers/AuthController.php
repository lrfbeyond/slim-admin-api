<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Models\Admin;
use App\Models\Role;
use Respect\Validation\Validator as v;
use App\Validate\Admin as valid;
use App\Controllers\LogController as Log;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Database\Capsule\Manager as DB;

class AuthController extends Controller
{
    public function index()
    {
        //
    }

    // 登录验证
    public function chkLogin($request, $response)
    {
        $res['result'] = 'failed';
        if ($request->isPost()) {
            $post = $request->getParsedBody();
            $username = injectCheck(htmlentities($post['username']));
            $rs = Admin::where('admin_name', $username)->where('is_delete', 0)->first(['id','admin_name','password','realname','role_id']);
            if ($rs) {
                // 验证码
                if ($post['code'] !== $_SESSION['hw-cphrase']) {
                    $res['msg'] = '验证码错误';
                    return $response->withJson($res);
                }
                // 验证密码
                if (password_verify($post['password'], $rs['password']) === false) {
                    $res['msg'] = '用户名或密码错误';
                    return $response->withJson($res);
                }

                $count = Admin::where('id', $rs['id'])->increment('login_counts');
                if ($count) {
                    $auth = [
                        'userid'    => $rs['id'],
                        'username'  => $rs['admin_name'],
                        'login_time' => time()
                    ];
                    $_SESSION['admin_auth'] = $auth;

                    // 获取当前用户角色权限
                    $_SESSION['admin_permission'] = $this->getUserPermission($rs['role_id']);
                    //print_r($_SESSION);exit;

                    $key = $this->safekey; //安全密钥
                    //$token = dataAuthSign($auth, $key);
                    //$_SESSION['admin_auth_sign'] = $token;
                    $res['result'] = 'success';
                    $res['uname'] = $rs['realname'];
                    $res['token'] = dataAuthSign($auth, $key);
                    Log::addLog('用户登录成功:【'.$post['username'].'】');
                } else {
                    $res['msg'] = '登录失败';
                }
                
            } else {
                $res['msg'] = '用户名或密码错误';
            }
        } else {
            $res['msg'] = '非法提交';
        }
        return $response->withJson($res);
    }

    // 修改密码
    public function editpass($request, $response)
    {
        $res['result'] = 'failed';
        if ($request->isPost()) {
            $post = $request->getParsedBody();
            $rules = [
                'newpass' => v::stringType()->notEmpty()->length(6, 16),
                'password' => v::notEmpty(),
            ];

            $valid = new valid;
            $validMsg = $valid->valid($request, $rules);
            if (!empty($validMsg)) {
                $res['msg'] = $validMsg;
                return $response->withJson($res);
            }

            $user = $_SESSION['admin_auth'];
            $rs = Admin::where('id', $user['userid'])->first(['password']);
            if ($rs) {
                // 验证原密码
                if (password_verify($post['password'], $rs['password']) === false) {
                    $res['msg'] = '原密码错误'; 
                    return $response->withJson($res);
                }

                // 创建新密码哈希值
                $passwordHash = password_hash(
                   $post['newpass'],
                   PASSWORD_DEFAULT,
                   ['cost' => 12]
                );

                $adm = Admin::find($user['userid']);
                $adm->password = $passwordHash;
                if ($adm->save()) {
                    $res['result'] = 'success';
                    Log::addLog('用户修改密码成功');
                    unset($_SESSION['admin_auth']);
                } else {
                    $res['msg'] = '修改失败';
                }
            } else {
                $res['msg'] = '数据出错';
            }
        } else {
            $res['msg'] = '非法提交';
        }
        return $response->withJson($res);
    }

    // 退出
    public function logout($request, $response)
    {
        $username = $_SESSION['admin_auth']['username'];
        unset($_SESSION['admin_auth']);
        $res['result'] = 'success';
        Log::addLog('用户退出:【'.$username.'】');
        return $response->withJson($res);
    }

    public function captcha()
    {
        $builder = new CaptchaBuilder;
        $builder->build(120,32);
        //$builder->save('out.jpg');

        $_SESSION['hw-cphrase'] = $builder->getPhrase();
        header('Content-type: image/jpeg');
        $builder->output();
    }

    // 获取当前用户角色权限
    private function getUserPermission($role_id)
    {
        $rs = Role::where('id', $role_id)->where('is_delete', 0)->first(['permission']);
        if ($rs) {
            $permission = explode(',', $rs['permission']);
            $perm = [];
            foreach ($permission as $key => $val) {
                $pers = DB::table('permission')->where('id', $val)->value('url');
                if ($pers) {
                    $perm[] = $pers;
                }
                 
            }
            return $perm;
        } else {
            return false;
        }
    }
}

