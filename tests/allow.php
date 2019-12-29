<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/31
 * Time: 16:23
 */
require_once __DIR__ . "/../vendor/autoload.php";

$demo = new \Aw\Upload\Single();
$demo->setUploadDir(__DIR__ . "/uploads","/uploads");
$demo->init();
$demo->setAsBlackList();
$ret = $demo->upload();

var_dump($ret, $demo->new_path, $demo->size, $demo->access);
