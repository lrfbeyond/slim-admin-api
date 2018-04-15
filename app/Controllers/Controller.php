<?php 
namespace App\Controllers;

class Controller
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function __get($property)
    {
        if ($this->container->{$property}) {
            return $this->container->{$property};
        }
    }

    public function _initialize()
    {
        header("Access-Control-Allow-Origin: *");
        //header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-token");
    }

    //数据签名认证
    // protected function dataAuthSign($data)
    // {
    //     //数据类型检测
    //     if(!is_array($data)){
    //         $data = (array)$data;
    //     }
    //     ksort($data); //排序
    //     $code = http_build_query($data); //url编码并生成query字符串
    //     $sign = sha1($code); //生成签名
    //     return $sign;
    // }
}
