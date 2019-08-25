# Lord Of SQL Injection
*darkelf to skeleton*

---
## darkelf
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect();  
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[pw])) exit("No Hack ~_~"); 
  if(preg_match('/or|and/i', $_GET[pw])) exit("HeHe"); 
  $query = "select id from prob_darkelf where id='guest' and pw='{$_GET[pw]}'"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id']) echo "<h2>Hello {$result[id]}</h2>"; 
  if($result['id'] == 'admin') solve("darkelf"); 
  highlight_file(__FILE__); 
?>
```
`id`만 `admin`으로 만들면 되지만, `or`와 `and`가 대소문자 구분 없이 필터링된다. 그래서 `or` 대신 `||`를 사용하여 문제를 풀 수 있었다.


---
## orge
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[pw])) exit("No Hack ~_~"); 
  if(preg_match('/or|and/i', $_GET[pw])) exit("HeHe"); 
  $query = "select id from prob_orge where id='guest' and pw='{$_GET[pw]}'"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id']) echo "<h2>Hello {$result[id]}</h2>"; 
   
  $_GET[pw] = addslashes($_GET[pw]); 
  $query = "select pw from prob_orge where id='admin' and pw='{$_GET[pw]}'"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if(($result['pw']) && ($result['pw'] == $_GET['pw'])) solve("orge"); 
  highlight_file(__FILE__); 
?>
```
처음에는 패스워드를 알아내고 `id`를 `admin`으로 만들어 문제를 해결해야 겠다고 생각했다. Blind SQL Injection으로 **orc** 문제처럼 패스워드를 하나 구하고, `id`를 `admin`으로 만들기 위해서 쿼리를 `pw='[password]' || id='admin'%23` 형태로 넘겨봤는데 풀리지 않았다.

그래서 `substr` 함수로 `id` 값에 대한 반응을 보려고 했다. 그런데 `substr(id, 1, 1) = 'g'`를 넣으면 `Hello guest`가, `'g'` 대신 `'a'`를 넣으면 `Hello admin`이 출력되는 것을 볼 수 있었다. 앞에서 구한 패스워드가 admin의 것이 아니라고 판단하고 다시 찾아내기로 했다.

`pw=' || id='admin' && ascii(substr, 1, 1)) < 97` 를 넣어봤는데, `&`를 파라미터 구분자로 인식해서 페이지에 쿼리문이 입력한대로 출력이 되지 않았다. 그래서 URL 인코딩한 값인 `&26`으로 대체하여 `admin`의 패스워드를 한 글자씩 알아냈다. 그리고 각각의 `id`와 `pw`를 묶어 쿼리를 날려보니 각 계정에 맞는 패스워드에만 반응을 보였고, 이를 통해 `admin`의 패스워드를 성공적으로 알아냈다고 판단했다.

그런데 `id`를 `admin`으로 만들고 `pw`에 `admin`의 패스워드를 넘겨주면 문제가 풀릴 것으로 생각했는데, `Hello admin`만 출력되고 별다른 반응은 없었다. 이 부분에서 한참을 헤맸는데, `pw`에 `admin`의 패스워드만 넘겨주면 첫 번째 쿼리는 조건문이 맞지 않아서 `Hello guest`는 출력되지 않지만, 두 번째 쿼리는 조건문이 맞아 정상적으로 작동하기 때문에 문제를 풀 수 있었다.


---
## troll
```php
<?php  
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(preg_match('/\'/i', $_GET[id])) exit("No Hack ~_~");
  if(preg_match("/admin/", $_GET[id])) exit("HeHe");
  $query = "select id from prob_troll where id='{$_GET[id]}'";
  echo "<hr>query : <strong>{$query}</strong><hr><br>";
  $result = @mysqli_fetch_array(mysqli_query($db,$query));
  if($result['id'] == 'admin') solve("troll");
  highlight_file(__FILE__);
?>
```
싱글쿼터와 `admin`이라는 단어가 필터링되는 상황에서 `id`에 `admin`을 넣어야 하는 문제이다.

`admin`이라는 단어가 그대로 들어가면 필터링되기 때문에 글자 사이에 `%00`을 넣어서 우회하려고 했다. 하지만 화면에 쿼리문이 출력되기는 했지만 널바이트로 인식해서 그런지 문제가 풀리지는 않았다. 그래서 `preg_match()` 우회 방법을 검색하다가 `\`가 있길래 사이에 넣어봤더니 문제가 풀렸다.


---
## vampire
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(preg_match('/\'/i', $_GET[id])) exit("No Hack ~_~");
  $_GET[id] = strtolower($_GET[id]);
  $_GET[id] = str_replace("admin","",$_GET[id]); 
  $query = "select id from prob_vampire where id='{$_GET[id]}'"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id'] == 'admin') solve("vampire"); 
  highlight_file(__FILE__); 
?>
```
이 문제는 `id`에 입력한 값을 소문자로 바꾼 후, `admin`이라는 단어를 공백으로 치환한다.

`str_replace` 함수는 입력한 문자열에서 치환하고자 하는 부분을 다른 문자열로 치환한다. `admin`이 공백으로 치환된 후에도 `admin`이 남아있으면 되기 때문에 `aadmindmin` 과 유사한 형태로 입력하여 문제를 풀 수 있었다.


---
## skeleton
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[pw])) exit("No Hack ~_~"); 
  $query = "select id from prob_skeleton where id='guest' and pw='{$_GET[pw]}' and 1=0"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id'] == 'admin') solve("skeleton"); 
  highlight_file(__FILE__); 
?>
```
`id`가 `admin`이 되면 풀리는 문제인데, 뒤에 `and 1=0`이라는 부분이 존재한다. 앞의 **AND**를 거짓으로 만들어 통과시키고 `id`에 `admin`을 넣은 후에 뒤의 **AND** 부분을 무력화하면 될 것이다. 그래서 `pw=' or id='admin'%23`을 입력하여 문제를 해결하였다.