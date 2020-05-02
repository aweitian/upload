<?php
/**
 * 单文件上传
 */

namespace Aw\Upload;


use OSS\Core\OssException;
use OSS\OssClient;

class AliOss
{
    public $error = "你没有上传文件,\$_FILES为空";
    public $max_size = 0;
    private $accessKeyId;
    private $accessKeySecret;
    private $endpoint;

    public function __construct($accessKeyId, $accessKeySecret, $endpoint = "oss-cn-shanghai.aliyuncs.com")
    {
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        $this->endpoint = $endpoint;
        $this->max_size = 2 * 1024 * 1024;
    }

    /**
     * 只上传一个成功，返回成功路径
     * 上传失败返回FALSE
     * 多个上传失败一个返回FALSE
     * 全部成功返回一个数组，里面是每个的路径
     * @return array|bool|mixed
     */
    public function upload()
    {
        $accessKeyId = $this->accessKeyId;
        $accessKeySecret = $this->accessKeySecret;
        $endpoint = $this->endpoint;
        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        } catch (OssException $e) {
            $this->error = $e->getMessage();
            return false;
        }
        $bucket = "b2b-nwj";
        $object = "upload/";
        foreach ($_FILES as $file) {
            if ($file['size'] > $this->max_size) {
                $this->error = $file['name'] . "文件大于2M，超过限制";
                return false;
            }
        }
        $ret = array();
        foreach ($_FILES as $file) {
            try {
                $info = $ossClient->putObject($bucket, $object . date("YmdHis", time()) . $file["name"], file_get_contents($file["tmp_name"]));
                if (!$info) {
                    $this->error = $file['name'] . "上传失败" . var_export($info, true);
                    return false;
                }
                if (!isset($info['info'])) {
                    $this->error = $file['name'] . "上传失败" . var_export($info, true);
                    return false;
                }
                if (!isset($info['info']['url'])) {
                    $this->error = $file['name'] . "上传失败" . var_export($info, true);
                    return false;
                }
                $ret[] = $info['info']['url'];
            } catch (OssException $e) {
                print $e->getMessage();
                return false;
            }
        }
        if (count($ret) == 1) {
            return $ret[0];
        }
        return $ret;
    }
}