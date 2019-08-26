# Lord Of SQL Injection
*golem to assassin*

---
## golem
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[pw])) exit("No Hack ~_~"); 
  if(preg_match('/or|and|substr\(|=/i', $_GET[pw])) exit("HeHe"); 
  $query = "select id from prob_golem where id='guest' and pw='{$_GET[pw]}'"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id']) echo "<h2>Hello {$result[id]}</h2>"; 
   
  $_GET[pw] = addslashes($_GET[pw]); 
  $query = "select pw from prob_golem where id='admin' and pw='{$_GET[pw]}'"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if(($result['pw']) && ($result['pw'] == $_GET['pw'])) solve("golem"); 
  highlight_file(__FILE__); 
?>
```
**orge** 문제와 유사하지만, 이 문제에서는 `or`, `and`, `substr`, `=`가 필터링된다. 그렇기 때문에 `substr` 함수 대신 `mid` 함수를, `=` 대신 `like`를 사용하여 우회해보기로 하였다.

`admin`의 패스워드 길이를 알아내기 위해 `pw=' || id like 'admin' && length(pw) like 8#` 같은 형식으로 입력해봤을 때, `Hello admin`이 출력되는 것으로 보아 일단 `like`는 정상적으로 사용할 수 있는 것으로 판단하였다.

이번에는 `substr` 함수를 대신하기 위해 `mid` 함수를 사용하여 패스워드를 한 글자씩 알아내보기로 하였다. `id like 'admin' %26%26 ascii(mid(pw, 1, 1)) < 97%23` 형태로 확인해보니 조건에 부합할 때만 `Hello admin`이 출력되는 것으로 보아 `mid` 함수 또한 정상적으로 사용할 수 있는 것으로 판단하였다.

위와 같이 함수를 대체하여 패스워드를 알아낸 다음에 `pw`에 대입하여 문제를 해결할 수 있었다.


---
## darkknight
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[no])) exit("No Hack ~_~"); 
  if(preg_match('/\'/i', $_GET[pw])) exit("HeHe"); 
  if(preg_match('/\'|substr|ascii|=/i', $_GET[no])) exit("HeHe"); 
  $query = "select id from prob_darkknight where id='guest' and pw='{$_GET[pw]}' and no={$_GET[no]}"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id']) echo "<h2>Hello {$result[id]}</h2>"; 
   
  $_GET[pw] = addslashes($_GET[pw]); 
  $query = "select pw from prob_darkknight where id='admin' and pw='{$_GET[pw]}'"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if(($result['pw']) && ($result['pw'] == $_GET['pw'])) solve("darkknight"); 
  highlight_file(__FILE__); 
?>
```
Blind SQL Injection 문제인데 `'`, `substr`, `ascii`, `=`가 필터링된다. 그래서 싱글쿼터 --> 더블쿼터, `substr` --> `mid`, `ascii` --> `ord`, `=` --> `like`로 대체하여 풀어보기로 하였다.

`pw`와 `no`를 빈칸으로 두고 **OR**로 이어서 `id like "admin"#`으로 `Hello admin`을 출력할 수 있는지 확인해봤는데 아무 반응이 없었다. 그래서 `pw`와 `no`에 아무 값이나 집어넣고 다시 확인해보니까 반응을 보였다.

`admin`의 패스워드를 구하기 위해 `id`에 `admin`을 넣고 **AND**로 연결한 다음, `length` 함수로 패스워드가 8자리인 것을 확인하고 `ord(mid(pw, 1, 1)) < 97`처럼 한 글자씩 확인해보니 반응을 보이는 것을 확인할 수 있었다.

위의 작업을 반복해서 전체 패스워드를 알아낸 후 `pw`에 입력하여 문제를 해결하였다.


---
## bugbear
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(preg_match('/prob|_|\.|\(\)/i', $_GET[no])) exit("No Hack ~_~"); 
  if(preg_match('/\'/i', $_GET[pw])) exit("HeHe"); 
  if(preg_match('/\'|substr|ascii|=|or|and| |like|0x/i', $_GET[no])) exit("HeHe"); 
  $query = "select id from prob_bugbear where id='guest' and pw='{$_GET[pw]}' and no={$_GET[no]}"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id']) echo "<h2>Hello {$result[id]}</h2>"; 
   
  $_GET[pw] = addslashes($_GET[pw]); 
  $query = "select pw from prob_bugbear where id='admin' and pw='{$_GET[pw]}'"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if(($result['pw']) && ($result['pw'] == $_GET['pw'])) solve("bugbear"); 
  highlight_file(__FILE__); 
?>
```
Blind SQL Injection 문제인데 필터링이 상당히 많다. `'`, `substr`, `ascii`, `=`, `or`, `and`, `공백`, `like`, `0x`가 필터링 되는 것을 볼 수 있다. 필터링 우회를 위해 더블쿼터, `mid`, `hex`, `instr`, `||`, `&&`, `%0a`를 대신 사용하기로 하였다.

`hex` 함수는 `or`이라는 문자열이 필터링되기 때문에 `ord` 함수 대신 사용하는 것으로, 16진수 값으로 반환해주는 역할을 한다.

`instr` 함수는 `=`와 `like`를 대신하는 함수로, 어떤 문자열에서 찾고자 하는 문자열이 있는지를 확인하여 해당 문자열이 위치하는 첫 번째 인덱스를 반환하고, 결과가 없으면 0을 반환한다. 문자열 전체를 검색하기 때문에 반환이 0과 1만 존재하므로 참/거짓 판단이 가능한 것이다.

먼저 `instr(id, "admin")`으로 `id`에 `admin`을 입력하여 `Hello admin`이 출력되는지를 확인했다. 그리고 `&&`로 이어주고 `length(pw) < 8` 처럼 값을 비교하여 패스워드가 8자리라는 것을 확인했다.

마지막으로 `mid(pw, 1, 1) < char(97)` 형식으로 아스키코드가 아닌 문자로 직접 비교하여 패스워드를 한 글자씩 찾아낸 후, 찾아낸 패스워드를 `pw`에 넣어 문제를 해결할 수 있었다.


---
## giant
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(strlen($_GET[shit])>1) exit("No Hack ~_~"); 
  if(preg_match('/ |\n|\r|\t/i', $_GET[shit])) exit("HeHe"); 
  $query = "select 1234 from{$_GET[shit]}prob_giant where 1"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result[1234]) solve("giant"); 
  highlight_file(__FILE__); 
?>
```
`shit`이라는 변수에 위와 같은 개행문자가 아니고, 1글자를 초과하지 않는 문자를 입력해야 쿼리가 정상적으로 작동할 것으로 보인다. 일반적으로 사용되는 개행문자를 다 넣어봤는데 `%0b`와 `%0c`가 우회가 가능해서 문제를 풀 수 있었다. 아래의 표에 개행문자를 정리해두었다.

| ASCII Code | Name |
| --- | --- |
| `%09` | Horizontal Tab(\t) |
| `%0a` | Line Feed(LF, \n) |
| `%0b` | Vertical Tab |
| `%0c` | Form Feed |
| `%0d` | Carriage Return(CR, \r) |
| `%20` | Space |


---
## assassin
```php
<?php 
  include "./config.php"; 
  login_chk(); 
  $db = dbconnect(); 
  if(preg_match('/\'/i', $_GET[pw])) exit("No Hack ~_~"); 
  $query = "select id from prob_assassin where pw like '{$_GET[pw]}'"; 
  echo "<hr>query : <strong>{$query}</strong><hr><br>"; 
  $result = @mysqli_fetch_array(mysqli_query($db,$query)); 
  if($result['id']) echo "<h2>Hello {$result[id]}</h2>"; 
  if($result['id'] == 'admin') solve("assassin"); 
  highlight_file(__FILE__); 
?>
```
쿼리문의 조건문 부분을 보면 등호가 아닌 `like`로 비교하고 있다. 하지만 싱글쿼터가 필터링되어 있기 때문에 해당 조건을 무시하는 것은 불가능하다.

확실히 싱글쿼터를 사용할 수 없다는 부분에서 문제를 어떻게 풀어야 할지 도저히 감이 잡히지 않아 막막했다. 조건문을 무시하고 `id`에 `admin`을 넣을 수 없기 때문이다. 그래서 `like` 관련해서 검색을 해보다가 해커스쿨에 있는 **rubiya**님의 SQL Injection 게시물을 다시 읽어보았다. 

`like` 쿼리는 **와일드카드**라는 것을 사용할 수 있는데, 문자열 뒤에 `%`를 붙이면 뒤에 어떤 문자열이 오는지 상관없이 **select**를 해주는 역할을 한다는 것을 알 수 있었다. 다른 변수를 입력해서 우회할 수 없다는 것은 Blind SQL Injection으로 패스워드를 알아내야 한다는 것인데, 게시물을 읽고 나서 생각이 난 것은 많이 아쉬웠다.

그동안의 패스워드 패턴이 숫자와 a~f 범위의 영어 소문자로 이루어져 있었기 때문에 해당 범위에서 한 글자씩 알아내기로 했다. 그런데 `Hello guest`만 출력되는 것을 볼 수 있었다.

와일드카드를 사용하면 검색 패턴을 만족하는 모든 데이터가 **select** 되기 때문에 `admin`과 `guest`의 패스워드가 비슷한 패턴을 가지고 있는 것으로 판단하였다. 그래서 문자를 계속 이어 붙여 검색을 시도했고, `admin`과 `guest`의 패스워드가 구분되는 지점에서 `Hello admin`이 출력되어 문제를 풀 수 있었다.