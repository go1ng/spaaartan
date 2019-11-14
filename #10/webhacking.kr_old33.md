# webhacking.kr (old-33)

## old-33
문제의 소스 코드를 볼 수 있는 링크가 있다. 적절한 요청을 보내면 다음 문제로 향하는 링크가 생성되었고, 이런 패턴이 반복되는 것을 확인할 수 있었다. 그렇게 여러 번 문제를 풀어야 해당 문제를 해결할 수 있을 것으로 보인다.

### 33-1
```php
<?php
if($_GET['get']=="hehe") echo "<a href=???>Next</a>";
else echo("Wrong");
?>
```
GET 방식으로 `get`이라는 변수에 `hehe`라는 값을 넘겨주면 되므로 URL을 통해 요청을 보내 문제를 해결한다.

### 33-2
```php
<?php
if($_POST['post']=="hehe" && $_POST['post2']=="hehe2") echo "<a href=???>Next</a>";
else echo "Wrong";
?>
```
Burp Suite로 body 부분에 값을 넘겨 보내려고 했는데 GET 방식과 POST 방식의 HTTP 패킷 형식이 서로 조금 달라서 바로는 풀리지 않았다. 그래서 그냥 페이지에 `<form>` 태그를 직접 삽입하여 위의 값으로 요청을 보냈다. *(GET/POST Method 한 번 정리해봐야겠다.)*

### 33-3
```php
<?php
if($_GET['myip'] == $_SERVER['REMOTE_ADDR']) echo "<a href=???>Next</a>";
else echo "Wrong";
?>
```
`$_SERVER['REMOTE_ADDR']` 는 접속한 클라이언트의 IP 주소를 나타내는 PHP의 환경 변수이다. 내 IP 주소를 확인할 수 있는 사이트에 접속하여 복사한 다음, URL에 `myip`로 IP 주소를 넘겨 요청을 보낸다.

### 33-4
```php
<?php
if($_GET['password'] == md5(time())) echo "<a href=???>Next</a>";
else echo "hint : ".time();
?>
```
`password` 와 현재 시간이 md5로 암호화된 값이 일치해야 문제가 풀린다. 하지만 즉시 md5로 암호화하여 요청을 보낼 수 없기 때문에 몇 초 정도 이후의 시간을 암호화한다. 그리고 URL에 미리 요청을 보낸 다음, 페이지 하단에 힌트로 준 `time()` 함수의 반환값을 확인하여 암호화한 시간에 맞게 새로고침 해준다.

### 33-5
```php
<?php
if($_GET['imget'] && $_POST['impost'] && $_COOKIE['imcookie']) echo "<a href=???>Next</a>";
else echo "Wrong";
?>
```
요청을 보낼 때 GET 방식 변수, POST 방식 변수, 쿠키값 모두 존재해야 한다. 쿠키 에디터로 `imcookie` 라는 이름으로 임의의 값을 넣어 쿠키를 만들어주고, URL과 `<form>` 태그를 이용하여 위의 GET/POST 방식 변수 모두에 임의의 값을 넘겨 요청을 보낸다.

### 33-6
```php
<?php
if($_COOKIE['test'] == md5($_SERVER['REMOTE_ADDR']) && $_POST['kk'] == md5($_SERVER['HTTP_USER_AGENT'])) echo "<a href=???>Next</a>";
else echo "hint : {$_SERVER['HTTP_USER_AGENT']}";
?>
```
`test` 라는 이름의 쿠키에 내가 접속한 IP 주소를 md5로 암호화하여 저장한다. 그리고 페이지 하단에 출력되는 User Agent를 md5로 암호화하고 `<form>` 태그에 암호화한 값을 넘겨 POST 방식으로 요청을 보낸다.

### 33-7
```php
<?php
$_SERVER['REMOTE_ADDR'] = str_replace(".","",$_SERVER['REMOTE_ADDR']);
if($_GET[$_SERVER['REMOTE_ADDR']] == $_SERVER['REMOTE_ADDR']) echo "<a href=???>Next</a>";
else echo "Wrong<br>".$_GET[$_SERVER['REMOTE_ADDR']];
?>
```
`$_SERVER['REMOTE_ADDR']` 환경 변수가 내가 접속한 IP 주소에서 `.`이 제거된 값으로 재할당된다. 재할당된 값으로 위의 형식을 맞추어 GET 방식으로 요청을 보낸다.

### 33-8
```php
<?php
extract($_GET);
if(!$_GET['addr']) $addr = $_SERVER['REMOTE_ADDR'];
if($addr == "127.0.0.1") echo "<a href=???>Next</a>";
else echo "Wrong";
?>
```
페이지가 로딩될 때 GET 방식 변수 배열로부터 변수를 가져온 후, `addr`이라는 변수가 존재하지 않으면 내가 접속한 IP 주소가 할당된다. 문제를 풀기 위해서는 `addr`이 위와 같은 IP 주소를 가지고 있어야 한다. URL에 GET 방식으로 위의 IP 주소를 넘겨 요청을 보내 문제를 해결한다.

### 33-9
```php
<?php
for($i=97;$i<=122;$i=$i+2){
  $answer.=chr($i);
}
if($_GET['ans'] == $answer) echo "<a href=???.php>Next</a>";
else echo "Wrong";
?>
```
위의 반복문이 종료되면 `$answer`에 어떤 문자열이 할당된다. 그 문자열을 `ans`에 넘겨 GET 방식으로 요청을 보내면 문제가 해결된다. 나는 `$answer`에 할당된 문자열을 알아내기 위해 처음에는 브라우저 콘솔에서 JavaScript 코드로 위의 반복문을 변환해보려고 했는데, 함수들이 헷갈려서 그냥 PHP 파일로 만들어 출력하였다.

### 33-10
```php
<?php
$ip = $_SERVER['REMOTE_ADDR'];
for($i=0;$i<=strlen($ip);$i++) $ip=str_replace($i,ord($i),$ip);
$ip=str_replace(".","",$ip);
$ip=substr($ip,0,10);
$answer = $ip*2;
$answer = $ip/2;
$answer = str_replace(".","",$answer);
$f=fopen("answerip/{$answer}_{$ip}.php","w");
fwrite($f,"<?php include \"../../../config.php\"; solve(33); unlink(__FILE__); ?>");
fclose($f);
?>
```
`$ip`는 내가 접속한 IP 주소가 할당되고, 아래의 여러 작업이 수행된 후에 최종적으로 `$answer`이라는 변수가 만들어진다. 그리고 지금 접속한 페이지 경로를 기준으로 `answerip/{$answer}_{$ip}.php` 파일이 생성된다. 파일에 작성되는 내용을 보면 **old-33** 문제를 해결할 수 있을 것으로 보인다. **33-9**처럼 PHP 파일로 만들어 여러 작업을 수행하여 파일 이름을 알아낸 다음, 해당 파일로 접근하여 문제를 해결하였다.