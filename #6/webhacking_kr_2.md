# Webhacking.kr
*50-100 Point / PHP, SQL, Server*

## #18 (SQL)
```php
<?php
if($_GET['no']){
  $db = dbconnect();
  if(preg_match("/ |\/|\(|\)|\||&|select|from|0x/i",$_GET['no'])) exit("no hack");
  $result = mysqli_fetch_array(mysqli_query($db,"select id from chall18 where id='guest' and no=$_GET[no]")); // admin's no = 2

  if($result['id']=="guest") echo "hi guest";
  if($result['id']=="admin"){
    solve(18);
    echo "hi admin!";
  }
}
?>
```
쿼리 결과값을 `admin`으로 만들어주기 위해서는 쿼리문에서 **AND**로 묶인 부분을 거짓으로 만든 다음, `no` 값을 2로 만들어주어야 한다. 그리고 공백 문자가 필터링되어 있기 때문에 `%0a`와 같이 다른 것으로 대체한다. `?no=0%0aor%0ano=2`와 같이 쿼리를 완성하여 문제를 해결한다. `no=2` 대신 `id='admin'` 으로 입력해도 무관하다.

---
## #24 (PHP)
```php
<?php
  extract($_SERVER);
  extract($_COOKIE);
  $ip = $REMOTE_ADDR;
  $agent = $HTTP_USER_AGENT;
  if($REMOTE_ADDR){
    $ip = htmlspecialchars($REMOTE_ADDR);
    $ip = str_replace("..",".",$ip);
    $ip = str_replace("12","",$ip);
    $ip = str_replace("7.","",$ip);
    $ip = str_replace("0.","",$ip);
  }
  if($HTTP_USER_AGENT){
    $agent=htmlspecialchars($HTTP_USER_AGENT);
  }
  echo "<table border=1><tr><td>client ip</td><td>{$ip}</td></tr><tr><td>agent</td><td>{$agent}</td></tr></table>";
  if($ip=="127.0.0.1"){
    solve(24);
    exit();
  }
  else{
    echo "<hr><center>Wrong IP!</center>";
  }
?>
```
`$REMOTE_ADDR` 값을 `127.0.0.1`로 조작하면 문제가 풀릴 것으로 판단하였다. 그래서 해당 변수를 변조할 수 있는 방법을 검색하다가 미리보기로 나오는 게시글의 대부분이 해당 문제의 풀이여서 쿠키를 이용한다는 힌트를 본의 아니게 알게 되었다. 코드 상단에 쿠키값을 추출하는 함수가 있는데, 이 부분에서 힌트를 캐치했어야 할 것으로 생각된다.

현재 접속한 IP를 `ip` 변수에 저장하고 필터링 과정을 거친 후에 `127.0.0.1`과 비교하여 일치하면 문제가 풀릴 것이다. 쿠키에 `REMOTE_ADDR`이라는 이름의 변수를 만들고, 값을 `112277....00....00....1`로 입력하여 필터링 된 결과를 `127.0.0.1`로 나오게 하면 문제가 풀린다.

---
## #26 (PHP)
```php
<?php
  if(preg_match("/admin/",$_GET['id'])) { echo"no!"; exit(); }
  $_GET['id'] = urldecode($_GET['id']);
  if($_GET['id'] == "admin"){
    solve(26);
  }
?>
```
`admin`이라는 문자열을 필터링 한 다음에 URL 디코딩을 거친다. 따라서 `admin`을 두 번 URL 인코딩하여 `id` 값으로 넣어주면 해결된다.

---
## #38* (Server)
문제를 보면 입력창이 존재한다. 페이지 소스 코드를 확인해보면 `admin.php` 라는 페이지가 존재한다는 것을 확인할 수 있다. 어드민 페이지로 이동해보면 입력했던 내용들이 로그로 IP와 함께 기록되어 있다. 그리고 `admin`으로 로그인해야 한다는 내용이 있다.

입력창에 `admin`을 입력해보면 어드민이 아니라는 내용이 출력된다. 나는 로그에 `admin`을 남기면 되는 문제라고 설명을 들어서 `admin`을 어떻게 우회해서 입력할 수 있을지만 생각했다. 하지만 알고 보니 로그 자체를 내가 생성하면 되는 문제였다. **Burp Suite**로 로그 형식을 갖춰 추가로 내용을 전달했더니 문제가 풀렸다. *(생각을 너무 안 했던 문제)*

---
## #39* (SQL)
```php
<?php
  $db = dbconnect();
  if($_POST['id']){
    $_POST['id'] = str_replace("\\","",$_POST['id']);
    $_POST['id'] = str_replace("'","''",$_POST['id']);
    $_POST['id'] = substr($_POST['id'],0,15);
    $result = mysqli_fetch_array(mysqli_query($db,"select 1 from member where length(id)<14 and id='{$_POST['id']}"));
    if($result[0] == 1){
      solve(39);
    }
  }
?>
```
처음에는 앞의 조건을 거짓으로 만들고 뒤를 참으로 만들어서 쿼리가 동작하도록 시도하였다. 하지만 싱글쿼터를 막으면 하나가 더 추가되기 때문에 불가능했다. 코드를 보면서 생각을 해보니까 `substr`이 괜히 있는 것이 아니라는 느낌이 들었다. 그런데 자르는 인덱스가 0부터 15까지라서 앞을 쓰레기 문자로 채워서 처리할 수도 없었다.

사람들의 풀이를 보면 `admin`과 싱글쿼터 사이를 공백으로 채워 추가되는 싱글쿼터가 잘려서 들어가지 못하도록 하였다. 내가 이 부분에서 이해하지 못하겠는 것은 `id`에 `admin`이 없는 경우에는 어떻게 풀이할 것인가이다. *(내가 생각을 잘못한 것인지, 재치가 없어서인지 참 답답하다.)*