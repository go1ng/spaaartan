# HackCTF(Web 100)
*(참 센스없는 건 여전하다.)*

---
### 보물
보물상자 아래에 버튼 3개가 있고 각각의 버튼을 누르면 `page` 변수에 1~3이 넘어가고 밑에 해시값 같은 문자열이 출력되었다. `page`에 다른 숫자를 넣어봐도 출력되었다.

복호화 하기에는 문자열이 너무 길어서 `hash-identifier`로 확인해보니까 SHA-512라는 결과가 나와서 포기했다.

페이지 숫자를 계속 늘려봐도 해시값만 나와서 대체 뭔가 싶었다. 페이지 숫자 중에 비밀이 있다고 하는데 도저히 감이 잡히지 않았다. 검색해볼 키워드도 생각이 나지 않아서 결국 풀이를 보기로 했다.

풀이에서는 Python의 `requests` 모듈로 페이지 숫자를 계속 키워서 플래그 키워드를 찾아냈다. 이 문제도 그렇고 Blind SQL Injection 문제도 그렇고 일일이 수작업으로 하는 건 정말 한계가 있어서 파이썬 공부의 필요성을 느꼈다.


---
### Guess me
```php
<?php
      $filename = 'secret.txt';
      extract($_GET);
      if (isset($guess)) {
        $secretcode = trim(file_get_contents($filename));
        if ($guess === $secretcode) {
          $flag = file_get_contents('flag.txt');
          echo "<p>flag is"." $flag</p>";
        } else {
          echo "<p>비밀 코드는 $guess (이)가 아닙니다. </p>";
        }
      }
?>
```
값을 넣어 제출하면 입력한 값이 GET 방식으로 `$guess` 에 전달된다. 그리고 `secret.txt` 파일의 내용을 가지고 있는 `$secretcode`와 비교하고, 두 값이 동일하면 플래그를 출력할 것이다.

게싱 문제라고 생각했는데 `secret.txt` 파일의 내용을 게싱하는 것은 도저히 말이 되지 않았다. `secret.txt` 파일에 직접 접근해봤는데 `그렇게 쉽겐 안되지` 라는 것만 출력되었다. 혹시 몰라서 `$guess`로 넘겨봤는데 가능할리가 없었다.

그래서 함수 취약점을 찾아봤는데 `extract`가 있었다. 해당 취약점에 대한 문서를 몇 개 읽어봤는데, 이 함수는 문제와 같이 `$_GET`으로 설정해놓으면 파라미터로 값을 넘겨 변수의 값을 조작할 수 있는 취약점을 가지고 있었다. 처음에는 내가 입력할 수 있는 변수가 `$guess`밖에 없다고 생각해서 막막했다.

GET 방식으로 넘어가는 변수를 조작할 수 있으니까 `$guess`와 `$secretcode`에 같은 값을 넣어봤는데 풀리지 않았다. 생각해보니까 `extract` 함수 이후에 변수가 선언되면 값을 조작할 수 없다.

그러면 `extract` 함수 이전에 선언되고 이후에 변경되지 않는 변수만을 조작할 수 있는 것인데, 위에 `$filename`이 있었다. `$filename`에 존재하지 않을 것 같은 파일명을 입력하고 `$guess`에 아무 값도 넣지 않았더니 문제가 풀렸다.


---
### Read File
처음으로 표시되는 것은 구글의 메인 페이지이다. URL을 보면 `command`에 구글의 홈페이지 주소가 적혀있는 것을 볼 수 있었다. 그리고 `flag.php` 파일을 읽으라고 한다.

다른 URL을 입력하거나 `/etc/passwd` 같은 파일 경로를 입력하면 화면에 출력이 되었다. 그래서 `flag.php`를 넣어봤는데 아무것도 출력이 되지 않았다.

파일 다운로드 취약점일 것이라고 판단하고 한 3일 동안 해당 취약점 관련해서 이것저것 검색해봤다. 그리고 파일 확장자가 필터링되어 있을 것이라고 생각해서 이것을 우회하려고 계속 시도해봤는데 도저히 풀리지 않았다.

조금만 더 풀어보려다가 인내심에 한계가 와서 풀이를 보기로 했다. 그런데 풀이를 보자마자 괜히 봤다는 생각이 딱 들었다. 나는 계속 확장자가 필터링되어 있다고만 생각했지, `flag`라는 단어가 필터링되어 있을 거라고는 생각도 못했다.

`flag`가 필터링되는 것으로 보아 `fflaglag` 처럼 입력해서 `flag` 라는 단어가 만들어질 수 있도록 해서 문제를 푸는 문제였는데, 분명 `str_replace` 함수가 사용되었을 것이다. **LOS** 풀면서 경험했던 문제였는데, 나도 참 센스가 없다.


---
### Login
```php
<?php
highlight_FILE(__FILE__);
require_once("dbcon.php");

$id = $_GET['id'];
$pw = $_GET['pw'];
$pw = hash('sha256',$pw);

$sql = "select * from jhyeonuser where binary id='$id' and pw='$pw'";
$result = mysqli_fetch_array(mysqli_query($db,$sql));

if($result['id']){
        $_SESSION['id'] = $result['id'];
        mysqli_close($db);
        header("Location:welcome.php");
}
?>
```
Username과 Password를 입력하면 GET 방식으로 요청되고 각각 `id`와 `pw`의 변수값으로 전달된다.
그리고 `$id`와 `$pw`로 데이터베이스에 질의하여 존재하는 계정이라면 `welcome.php`로 이동한다.

해당 데이터베이스에 어떤 계정이 존재하는지 알 수 없기 때문에 **SQL Injection**을 통해 문제를 풀어보기로 하였다.

`$id`에 `' or 1=1#` 입력하면 참이 되고 나머지는 주석 처리되기 때문에 `welcome.php`로 이동되고 플래그가 출력되는 것을 확인할 수 있었다.


---
### 마법봉
```php
<?php
show_source(__FILE__);
$flag = "if_you_solved";
$input = $_GET['flag'];
if(md5("240610708") == sha1($input)){
    echo $flag;
}
else{
    echo "Nah...";
}
?>
```
분명 예전에 봤던 문제라는 느낌은 받았는데 확실히 감이 오지 않아서 코드의 일부분을 검색하고 나서야 **매직해시(Magic Hash) 취약점** 문제라는 것을 깨달았다. 문제 속에 힌트가 있다는 것을 항상 명심해야 하는데 또 간과했다.

PHP에서는 0e1234처럼 0e로 시작하고 뒤에 숫자만으로 이루어진 값은 모두 0으로 인식한다. e를 기준으로 앞뒤로 숫자만으로 이루어져 있으면 지수 형태의 값으로 인식하여 0^n이 되기 때문에 모두 0이 되는 것이다.

소스코드에서 볼 수 있듯이 md5로 **'240610708'** 을 암호화하면 **'0e462097431906509019562988736854'** 라는 값을 얻을 수 있고 0e 뒷부분이 모두 숫자로 구성된 것을 확인할 수 있다. sha1 또한 **'10932435112'** 를 암호화하면 **'0e07766915004133176347055865026311692244'** 라는 값을 얻을 수 있다.

두 매직해시를 비교하면 0=0으로 참이 되기 때문에 플래그가 출력되는 것을 볼 수 있었다.
