curl -F img=@/c/0037.png -F xxx=@/c/frl.gif http://127.0.0.1:1259/tests/upload.php

curl -F xxx=@/c/q.png http://127.0.0.1:1259/tests/single.php

### upload test result：
<pre>
array(3) {
  ["code"]=>
  int(200)
  ["message"]=>
  string(2) "OK"
  ["data"]=>
  array(2) {
    ["succ"]=>
    array(2) {
      ["img"]=>
      string(33) "/uploads/user/201912291047594.png"
      ["xxx"]=>
      string(33) "/uploads/user/201912291047595.gif"
    }
    ["fail"]=>
    array(0) {
    }
  }
}
</pre>

### single test result:
<pre>
bool(true)
string(55) "E:\github\upload\tests/uploads\user\201912291052388.png"
int(5738)
string(33) "/uploads/user/201912291052388.png"
</pre>

### not allow test result:
<pre>
bool(false)
string(38) "regedit.exe文件类型不允许上传"
</pre>
