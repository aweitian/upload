<?php
/**
 * 单文件上传
 */

namespace Aw\Upload;


class Single extends Common
{
    public $error = "你没有上传文件,\$_FILES为空";
    public $origin_name;
    public $new_path;
    public $size;
    public $access;
    public $type;

    /**
     * @return bool
     */
    public function upload()
    {
        if (!is_array($_FILES)) {
            return false;
        }
        foreach ($_FILES as $fk => $file) {
            switch ($this->chk($file)) {
                case 2:
                    $this->error = $file["name"] . "文件大小过大";
                    return false;
                case 3:
                    $this->error = $file["name"] . "文件类型不允许上传";
                    return false;
                case 4:
                    $this->error = "空间目录不允许上传";
                    return false;
                case 5:
                    $error = array(
                        "1" => "上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。",
                        "2" => "上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。",
                        "3" => "文件只有部分被上传。",
                        "4" => "没有文件被上传。"
                    );
                    $en = $file["error"];
                    if (array_key_exists(strval($en + 1), $error)) {
                        $this->error = $error[$en + 1];
                    } else {
                        $this->error = $file["name"] . "未知错误";
                    }
                    return false;
                case 0:
                    $filename = $this->getFileName($file);
                    $this->origin_name = $file['name'];
                    $tmpName = $file["tmp_name"];
                    $new_path = $this->directory . DIRECTORY_SEPARATOR . $this->sub_dir . DIRECTORY_SEPARATOR . $filename;
                    if (function_exists("move_uploaded_file") && @move_uploaded_file($tmpName, $new_path)) {
                        @chmod($filename, 0666);
                        $this->new_path = $new_path;
                        $this->size = $file["size"];
                        $this->access = $this->web_dir . '/' . $this->sub_dir . '/' . $filename;
                        $this->type = $file["type"];
                        return true;
                    } elseif (@copy($tmpName, $new_path)) {
                        @chmod($filename, 0666);
                        $this->new_path = $new_path;
                        $this->size = $file["size"];
                        $this->access = $this->web_dir . '/' . $this->sub_dir . '/' . $filename;
                        $this->type = $file["type"];
                        return true;
                    } else {
                        $this->error = $file["name"] . "不能移动临时文件";
                    }
            }
        }
        return false;
    }
}