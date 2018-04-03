<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/31
 * Time: 16:23
 */
require_once __DIR__ . "/../vendor/autoload.php";

$demo = new \Aw\Upload\Common();
$demo->setUploadDir(__DIR__ . "/uploads");
$demo->init();
$ret = $demo->upload();

var_dump($ret);
