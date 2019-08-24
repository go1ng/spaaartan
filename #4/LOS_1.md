# Lord Of SQL Injection
*gremlin to wolfman*

---
## gremlin
```php
<?php
  include "./config.php";
  login_chk();
  $db = dbconnect();
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[id])) exit("No Hack ~_~"); // do not try to attack another table, database!
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[pw])) exit("No Hack ~_~");
  $query = "select id from prob_gremlin where id='{$_GET[id]}' and pw='{$_GET[pw]}'";
  echo "<hr>query : <strong>{$query}</strong><hr><br>";
  $result = @mysqli_fetch_array(mysqli_query($db,$query));
  if($result['id']) solve("gremlin");
  highlight_file(__FILE__);
?>
```
쿼리문을 보면 `id`와 `pw`가 필요한데 `id`만 데이터베이스에 존재하면 되므로 `id`를 참으로 만들고 나머지 부분을 주석 처리하면 될 것이다.

`id`에 `' or 1=1#`를 입력해봤는데 `#`이 URL 인코딩되지 않아서 인식을 하지 못했다. 그래서 `%23`으로 입력하니까 문제를 해결할 수 있었다.


---
## cobolt
```php
<?php
  include "./config.php"; 
  login_chk();
  $db = dbconnect();
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[id])) exit("No Hack ~_~"); 
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[pw])) exit("No Hack ~_~"); 
  $query = "select id from prob_cobolt where id='{$_GET[id]}' and pw=md5('{$_GET[pw]}')"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id'] == 'admin') solve("cobolt");
  elseif($result['id']) echo "<h2>Hello {$result['id']}<br>You are not admin :(</h2>"; 
  highlight_file(__FILE__); 
?>
```
`id`가 존재하면 `admin`이 아니라고 출력되고, `id`가 `admin`이면 문제를 풀 수 있다.

`id`가 `admin`이 되고 나머지 쿼리는 무시할 수 있도록 `id`에 `admin'%23`을 입력하여 문제를 해결할 수 있었다.


---
## goblin
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[no])) exit("No Hack ~_~"); 
  if(preg_match('/\'|\"|\`/i', $_GET[no])) exit("No Quotes ~_~"); 
  $query = "select id from prob_goblin where id='guest' and no={$_GET[no]}"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id']) echo "<h2>Hello {$result[id]}</h2>"; 
  if($result['id'] == 'admin') solve("goblin");
  highlight_file(__FILE__); 
?>
```
`id`는 `guest`로 고정되어 있고 `no`만 입력할 수 있는데, `id`의 `no`가 일치하면 쿼리가 동작하여 `guest`가 화면에 출력된다. 하지만 문제를 풀기 위해서는 `id`를 `admin`으로 만들어야 한다.

쿼리문을 보면 `id`와 `no`가 **AND**로 연결되어 있기 때문에 둘 중 하나가 틀리면 거짓이 되어 작동하지 않을 것이다. 그리고 **OR**로 쿼리문을 이어주면 **OR** 이후의 쿼리는 정상적으로 작동하게 될 것이다.

`false or id='admin'%23`로 `id`에 `admin`을 넘기려고 했는데 위에 쿼터를 필터링하는 구문이 있다는 것을 깜빡했다. 그래서 싱글쿼터를 URL 인코딩해서 `%27`로 입력해보았지만 필터링되었다. 아예 `admin`만 입력해봤는데 이번에는 반응이 없었다.

다른 방법을 찾다가 헥사값으로도 입력이 가능하다는 것을 알게 되었다. 그래서 `'admin'`을 ASCII Hex 값으로 인코딩하여 `0x2761646d696e27`를 넣어봤는데 반응이 없었다. 그래서 쿼터를 제거한 `0x61646d696e`를 넣으니 문제가 해결되었다.


---
## orc
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[pw])) exit("No Hack ~_~"); 
  $query = "select id from prob_orc where id='admin' and pw='{$_GET[pw]}'"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id']) echo "<h2>Hello admin</h2>"; 
   
  $_GET[pw] = addslashes($_GET[pw]); 
  $query = "select pw from prob_orc where id='admin' and pw='{$_GET[pw]}'"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if(($result['pw']) && ($result['pw'] == $_GET['pw'])) solve("orc"); 
  highlight_file(__FILE__); 
?>
```
문제가 해결되는 조건을 보면 `admin`의 `pw`와 입력한 `pw`가 일치해야 하는 것으로 보인다. 그렇기 때문에 `pw`를 참으로 만든다고 해도 데이터베이스에 저장된 `pw`와 일치하지 않기 때문에 문제를 해결할 수 없고, **Blind SQL Injection**을 시도하여 값을 직접 알아내야 한다.

`pw`를 닫고 뒤에 **true**를 넣으면 쿼리가 작동하여 'Hello admin'이 출력되는 것을 확인하였다. 값을 대입해보면서 비교 조건이 참이 되면 'Hello admin'이 출력되고 거짓이면 출력되지 않는 반응을 이용해 유추할 수 있는 것이다.

먼저 `pw`의 길이를 알아내기 위해서 `length` 함수를 사용하였다. `pw=' or length(pw) < 4#` 와 같은 형태로 비교하여 8자리인 것을 확인할 수 있었다. `pw`가 `admin`의 것이 아닌지 혹시 몰라서 함수에 `id`를 넣어봤는데 5자리인 것으로 보아 옳은 결과라고 판단하였다.

다음은 `substr` 함수와 `ascii` 함수를 이용하여 한 글자씩 알아내야 한다. 워낙 귀찮은 작업이라 소스코드를 작성하여 자동으로 정리할 수 있도록 하면 되는데, 아직 실력이 부족한 탓에 수작업으로 진행하였다.

`ascii(substr(pw, 1, 1)) < 97#` 처럼 아스키코드와 문자열 인덱스 값에 변화를 주며 페이지의 반응을 일일히 확인하여 `admin`의 `pw` 값을 구할 수 있었고, 값을 `pw`에 대입하여 문제를 해결하였다.


---
## wolfman
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[pw])) exit("No Hack ~_~"); 
  if(preg_match('/ /i', $_GET[pw])) exit("No whitespace ~_~"); 
  $query = "select id from prob_wolfman where id='guest' and pw='{$_GET[pw]}'"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id']) echo "<h2>Hello {$result[id]}</h2>"; 
  if($result['id'] == 'admin') solve("wolfman"); 
  highlight_file(__FILE__); 
?>
```
`id`를 `admin`으로 만들면 해결되는 문제이지만, `pw`는 공백이 필터링된다. 직접 확인해보면 알겠지만 `pw`를 싱글쿼터로 닫고 이어서 입력해도 역시 필터링된다.

이를 우회하기 위하여 공백을 `%0a`로 처리하였다. `pw='%0aor%0aid='admin'%23`를 입력하면 `pw=' or id='admin'#`으로 처리되어 문제를 해결할 수 있었다.