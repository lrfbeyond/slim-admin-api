<?php 
namespace App\Controllers;

use Psr\Log\LoggerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Controllers\LogController as Log;

class SettingController extends Controller
{
    public function index($request, $response)
    {
        $c = $this->container;
        $settings = $c->settings;
        $customer = $settings['customer'];
        // foreach ($customer as & $val) {
        //     if (is_bool($val)) {
        //         //$val = ($val == true) ? 'true' : 'false';
        //     }
        // }
        return $response->withJson($customer);
    }

    public function setOk($request, $response)
    {
        $res['result'] = 'failed';
        if ($request->isPost()) {
            $post = $request->getParsedBody();
            $rep = [];
            if (is_array($post) && count($post) > 0) {
                foreach ($post as $key => & $val) {
                    $pat[] = $key;
                    if (is_bool($val)) {
                        $rep[] = $val == 1 ? 'true' : 'false';
                    } else {
                        $rep[] = $val;
                    }
                }
            }
            // print_r($pat);
            // print_r($rep);
            $rs = $this->setconfig($pat, $rep);
            if (true === $rs) {
                $res['result'] = 'success';
            }
        }
        return $response->withJson($res);
    }
   
    /**
     * 修改配置文件
     * 传递的参数为2个数组 前面的为配置 后面的为数值.  正则的匹配为单引号  如果你的是分号 请自行修改为分号
     * $pat[0] = 参数前缀;  例:   default_return_type
       $rep[0] = 要替换的内容;    例:  json
     */
    private function setconfig($pat, $rep)
    {
        
        if (is_array($pat) && is_array($rep)) {
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

