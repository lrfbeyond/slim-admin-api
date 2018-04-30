<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers\LogController as Log;

class SettingController extends Controller
{
    // 基本设置
    public function saveSetting()
    {
        $res['result'] = 'failed';
        $auth = $this->chkModAuth('/network/', 1);
        if ($auth) {
            $res['code'] = cfError(207);
            return $res;
        }
        if (request()->isPost()) {
            try {
                $post = input('post.');
                foreach ($post as $key => $val) {
                    Db::name('setting')->where('name', $key)->update(['value' => $val]);
                }

                $pat = [
                    'use_camera',
                    'wx_login',
                    'wx_appid',
                    'wx_appkey'
                ];

                $camera_enable = $post['camera_enable'] == 1 ? 'true' : 'false';
                $wxlogin_enable = $post['wxlogin_enable'] == 1 ? 'true' : 'false';

                $rep = [
                    $camera_enable,
                    $wxlogin_enable,
                    $post['wx_appid'],
                    $post['wx_appkey']
                ];
                $rs = $this->setconfig($pat, $rep);

                $res['result'] = 'success';
                $this->addLog(['event_id' => 14, 'note' => '保存基本设置']);
            } catch (\Exception $e) {
               $res['code'] = cfError(-1);
               return $res;
            }
        } else {
            $res['code'] = cfError(201); // 非法请求
        }
        return $res;
    }

    /**
     * 修改配置文件
     * 传递的参数为2个数组 前面的为配置 后面的为数值.  正则的匹配为单引号  如果你的是分号 请自行修改为分号
     * $pat[0] = 参数前缀;  例:   default_return_type
       $rep[0] = 要替换的内容;    例:  json
     */
    private function setconfig($pat, $rep)
    {
        
        if (is_array($pat) and is_array($rep)) {
            for ($i = 0; $i < count($pat); $i++) {
                $pats[$i] = '/\'' . $pat[$i] . '\'(.*?),/';
                if ($rep[$i] == 'true' || $rep[$i] == 'false') {
                    $reps[$i] = "'". $pat[$i]. "'". " => " .$rep[$i] .",";
                } else {
                    $reps[$i] = "'". $pat[$i]. "'". " => " . "'".$rep[$i] ."',";
                }
            }
            $fileurl =  "../app/setting.php";

            $string = file_get_contents($fileurl); //加载配置文件
            $string = preg_replace($pats, $reps, $string); // 正则查找然后替换
            file_put_contents($fileurl, $string); // 写入配置文件
            return true;
        } else {
            return flase;
        }
    }
}

