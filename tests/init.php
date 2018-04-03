<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/31
 * Time: 16:23
 */
require_once __DIR__ . "/../vendor/autoload.php";

$demo = new \Aw\Upload\Common();
try {
    $demo->setUploadDir(__DIR__ . "/noexist");
} catch (\Exception $exception) {
    print $exception->getMessage();
}

Aw\Filesystem\Filesystem::createDir(__DIR__.'/uploads');

$demo->setUploadDir(__DIR__ . "/uploads");
