<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/31
 * Time: 16:23
 */
require_once __DIR__ . "/../vendor/autoload.php";

$demo = new \Aw\Upload\AliOss('LTAI4FoViUCVLkHwwtwPzTrF','Ph6JFTLCbbBB0GOmJkVZI1999GaGUw');

$demo->max_size = 3 * 1024 * 1024;

$ret = $demo->upload();

var_dump($ret);



/*
 * $ curl -F img=@/c/0037.png http://127.0.0.1:1259/tests/oss.php
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100  326k    0    87  100  326k     66   248k  0:00:01  0:00:01 --:--:--  248k
string(73) "http://b2b-nwj.oss-cn-shanghai.aliyuncs.com/upload/202005021141330037.png"


Administrator@SC-202002261051 MINGW32 ~/Desktop
$ curl -F img=@/c/0037.png -F xxx=@/c/frl.gif http://127.0.0.1:1259/tests/oss.php
  % Total    % Received % Xferd  Average Speed   Time    Time     Time  Current
                                 Dload  Upload   Total   Spent    Left  Speed
100  373k    0   206  100  373k    151   274k  0:00:01  0:00:01 --:--:--  274k
array(2) {
  [0]=>
  string(73) "http://b2b-nwj.oss-cn-shanghai.aliyuncs.com/upload/202005021142080037.png"
  [1]=>
  string(72) "http://b2b-nwj.oss-cn-shanghai.aliyuncs.com/upload/20200502114208frl.gif"
}

 */