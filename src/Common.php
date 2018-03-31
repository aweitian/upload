<?php
/**
 *
 * array(3) {
 * ["file1"]=>
 * array(5) {
 * ["name"]=>
 * string(9) "newbd.txt"
 * ["type"]=>
 * string(10) "text/plain"
 * ["tmp_name"]=>
 * string(14) "/tmp/phpEvmU8X"
 * ["error"]=>
 * int(0)
 * ["size"]=>
 * int(5680)
 * }
 * ["file2"]=>
 * array(5) {
 * ["name"]=>
 * string(8) "a.pcapng"
 * ["type"]=>
 * string(24) "application/octet-stream"
 * ["tmp_name"]=>
 * string(14) "/tmp/php8G4oLH"
 * ["error"]=>
 * int(0)
 * ["size"]=>
 * int(852356)
 * }
 * ["file3"]=>
 * array(5) {
 * ["name"]=>
 * string(6) "use.js"
 * ["type"]=>
 * string(22) "application/javascript"
 * ["tmp_name"]=>
 * string(14) "/tmp/phpBRs5ur"
 * ["error"]=>
 * int(0)
 * ["size"]=>
 * int(1832)
 * }
 * }
 */

namespace Aw\Upload;

use Aw\Filesystem\Filesystem;

class Common
{
    const CONTENT_TYPE_JPG = "image/jpeg";
    const CONTENT_TYPE_GIF = "image/gif";
    const CONTENT_TYPE_PNG = "image/png";
    protected $directory; //上传至目录
    protected $maxsize; //最大上传大小
    protected $allowType; //上传类型
    protected $rndFileMode;//true为随机文件名，FALSE保持原文件名

    protected $sub_dir;

    public function setUploadDir($dir)
    {
        if (!is_dir($dir)) {
            throw  new \Exception("$dir is not exist");
        }
        $this->directory = $dir;
    }


    /**
     * 默认上传2M，只能上传JPG,GIF,PNG图片
     * 上传路径为/uploads/user/目录下
     * 上传后的文件名为随机文件名
     */
    public function init()
    {
        $this->maxsize = 2097152;//(1024*2)*1024=2097152 就是 2M
        $this->setSaveDir("user");
        $this->allowType = array();
        $this->allowType[] = self::CONTENT_TYPE_JPG;
        $this->allowType[] = self::CONTENT_TYPE_GIF;
        $this->allowType[] = self::CONTENT_TYPE_PNG;
        $this->rndFileMode = true;
    }

    /**
     * 单位是M
     * @param int $size
     * @return $this
     */
    public function setMaxSize($size)
    {
        $this->maxsize = $size * 1024 * 1024;
        return $this;
    }

    /**
     *
     * @param int $type httpResponse::CONTENT_TYPE_JPG
     * @return $this
     */
    public function addAllowType($type)
    {
        $this->allowType[] = $type;
        return $this;
    }

    /**
     *
     * @param array $type
     * @return $this
     */
    public function setAllowType($type)
    {
        $this->allowType = $type;
        return $this;
    }

    /**
     *
     * @param bool $mode
     * @return $this
     */
    public function setRndFileMode($mode)
    {
        $this->rndFileMode = !!$mode;
        return $this;
    }

    /**
     * 只能是aaa/bb/cc
     * @param string $dir
     * @return boolean
     */
    public function setSaveDir($dir)
    {
        if (preg_match("/^\w+(\/\w+)*$/", $dir)) {
            if (!is_dir($this->directory . DIRECTORY_SEPARATOR . $dir)) {
                if (!Filesystem::createDir($this->directory . DIRECTORY_SEPARATOR . $dir)) {
                    return false;
                }
            }
            $this->sub_dir = $dir;
            return true;
        }
        return false;
    }

    /**
     * 有一个成功返回成功，全部失败返回失败，RESULT为失败个数
     * info        为上传成功多少个
     * return    包含两个数组succ,fail,SUCC以NAME为KEY，FAIL普通数组，一级错误信息
     * @return array
     */
    public function upload()
    {
        $return = array(
            'code' => 200,
            'message' => 'OK',
            'data' => array(
                "succ" => array(),
                "fail" => array()
            )
        );
        if (!is_array($_FILES)) {
            return array('code' => 500, 'message' => 'invalid upload mode', 'data' => array());
        }
        foreach ($_FILES as $fk => $file) {
            switch ($this->chk($file)) {
                case 2:
                    $return['data']["fail"][] = $file["name"] . "文件大小过大";
                    break;
                case 3:
                    $return['data']["fail"][] = $file["name"] . "文件类型不允许上传";
                    break;
                case 4:
                    $return['data']["fail"][] = "空间目录不允许上传";
                    break;
                case 5:
                    $error = array(
                        "1" => "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。",
                        "2" => "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。",
                        "3" => "文件只有部分被上传。",
                        "4" => "没有文件被上传。"
                    );
                    $en = $file["error"];
                    if (array_key_exists(strval($en + 1), $error)) {
                        $return['data']["fail"][] = $error[$en + 1];
                    } else {
                        $return['data']["fail"][] = $file["name"] . "未知错误";
                    }
                    break;
                case 0:
                    $filename = $this->getFileName($file);
                    $tmpName = $file["tmp_name"];
                    $new_path = $this->directory . DIRECTORY_SEPARATOR . $this->sub_dir . DIRECTORY_SEPARATOR . $filename;
                    if (function_exists("move_uploaded_file") && @move_uploaded_file($tmpName, $new_path)) {
                        @chmod($filename, 0666);
                        $return['data']["succ"][$fk] = $new_path;
                        break;
                    } elseif (@copy($tmpName, $new_path)) {
                        @chmod($filename, 0666);
                        $return['data']["succ"][$fk] = $new_path;
                        break;
                    } else {
                        $return['data']["fail"][] = $file["name"] . "不能移动临时文件";
                    }
            }
        }
        return $return;
    }

    protected function chk($file)
    {
        if ($file["error"] != 0) return 5;
        if ($file["size"] > $this->maxsize) return 2;
        if (!in_array($file["type"], $this->allowType)) return 3;
        if (!is_writable($this->directory)) return 4;
        return 0;
    }

    protected function getFileName($file)
    {
        if ($this->rndFileMode) {
            $basename = date("YmdHis") . rand(0, 9);
            return $basename . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
        } else {
            return $file["name"];
        }
    }
}