# 그누보드5 설치하기

### 설명
기존에 웹서버 구축을 완료하였기 때문에 바로 그누보드 설치

**sir** 사이트의 [매뉴얼](https://sir.kr/manual/g5/2)을 참조하여 **gnuboard 5.3.2.0** 설치

임시로 MySQL에 그누보드를 위한 계정과 데이터베이스 생성

![gnuboard5_db](https://raw.githubusercontent.com/arachnex/spartan/master/%235/gnuboard5_db.png "DB Info")

![gnuboard5_files](https://raw.githubusercontent.com/arachnex/spartan/master/%235/gnuboard5_files.png "Server Files")

![gnuboard5_main](https://raw.githubusercontent.com/arachnex/spartan/master/%235/gnuboard5_main.png "Main Page")

---
### 문제 해결
설치 도중에 MySQL 정보 입력하는 부분에서 connect error 발생

```sh
mysql> grant all privileges on g5.* g5@localhost identified by 'password';
```
위의 명령어를 입력하여 계정에 그누보드 데이터베이스의 모든 테이블 사용 권한을 부여
