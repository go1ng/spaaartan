# CrazyDog$_2nd
*Web*

*Object: Get shell.*

*공통적으로 쉘코드를 어떻게 작동시킬 수 있을지 감이 잡히지 않는다.*

## CookieWithSerial*
문제에 접속해보니 유저 정보와 소스코드가 나와 있었다. 소스 코드는 전체적으로 유저 정보를 설정하고 출력해주는 구조였다.
<br><br>

```php
if(isset($_COOKIE['session'])) {
    $this->user = unserialize(base64_decode($_COOKIE['session']));
}
```
`Client` 클래스에서 생성자 부분에 유저 정보를 설정하는 두 번째 방법을 보면, 쿠키에 존재하는 `session` 값을 base64 디코딩하고 `unserialize` 하는 것을 확인할 수 있었다. 여기서 쿠키에 쉘코드를 삽입하면 쉘을 획득할 수 있겠다는 생각이 들었다.
<br><br>

```
O:4:"User":2:{s:4:"name";s:5:"guest";s:4:"addr";s:9:"127.0.0.1";}
```
`session` 값을 base64 디코딩 해보니까 생소한 표현이 나왔고, 직렬화(**Serialize**)라는 개념이 필요했다. 검색을 해보니 직렬화는 값의 저장 가능한 표현을 만들어주는 것으로, 자료형에 따라 표현이 달랐다. 이는 자료형이나 구조의 손실 없이 PHP 값을 저장하거나 전달하는데 유용하다고 한다.

`session` 값을 디코딩해서 본 내용은 `User` 객체의 직렬화된 표현이었다. 그래서 변형된 값을 형식에 맞추어 쿠키를 바꾸면 정상적으로 내용이 출력될 것으로 예상하였다. 그래서 `addr` 변수의 값 대신에 `shell_exec()` 와 같은 함수를 넣어서 명령어를 실행시킬 수 있는지 확인해 보았지만 입력한 함수 내용이 텍스트 그대로 출력이 되는 것을 볼 수 있었다.

여기서부터 쉘코드를 어떻게 삽입해야 할지 감이 잡히지 않았다. 

**PHP Object Injection?**

*(공부할 것 - PHP OOP, Shellcode)*

---
## XMLParser*
XML 형식의 문자열을 읽어들여 객체로 반환한 다음, 내용을 출력해주는 형태의 페이지로 파악된다.

```php
if(isset($_REQUEST['data'])) {
    $data = $_REQUEST['data'];
}

else 
    $data =
    "<?xml version='1.0' encoding='UTF-8'?>
    <user>
    <id>1337</id>
    <name>guest</name>
    </user>";
```
위 코드에서 `id`와 `name`의 값을 변경하여 `data` 변수를 통해 요청을 보내면 입력한 내용이 페이지에 출력되는 것을 확인할 수 있었다. 여기서도 역시 입력한 값이 텍스트로 처리되어 함수와 같은 것들이 작동되지 않았다.

객체의 내용을 `echo` 할 때 우회를 하거나 조작을 해주는 것이 맞는 것인가?