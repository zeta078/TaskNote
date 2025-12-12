프로젝트 제목 : TaskNote

PHP + MySQL(MariaDB) 기반의 간단한 할 일/목표 + 메모 관리 웹 프로젝트입니다.  
회원가입/로그인/세션 기반으로 동작하며, 로그인 상태에서만 데이터 조회/등록이 가능합니다.

## 주요 기능
- 회원가입 / 로그인 / 로그아웃 (세션 기반)
- 할 일(목표) CRUD
- 메모 CRUD
- 로그인 시 메인에서 최근 할 일/메모 요약 표시

## 실행 환경
- Windows + XAMPP (Apache, MySQL)
- PHP
- phpMyAdmin

## 설치 및 실행 방법
1) XAMPP 실행 → **Apache / MySQL** Start  
2) 프로젝트 폴더를 `C:\xampp\htdocs\` 아래에 위치  
   예: `C:\xampp\htdocs\202168033\tasknote\`
3) 브라우저 접속  
   - `http://localhost/202168033/tasknote/main.php`

## DB 설정
- DB 이름: `phpproject_db`
- DB 파일 이름 : `db/phpproject_db.sql`

### DB Import 방법 (phpMyAdmin)
1) phpMyAdmin 접속
2) DB `phpproject_db` 생성
3) **가져오기(Import)** → `db/phpproject_db.sql` 선택 → 실행

## 폴더 구조
- `main.php` : 메인(로그인 전/후 화면 분기)
- `login.php` / `logout.php` : 로그인/로그아웃
- `register.php` : 회원가입
- `todo.php` : 할 일/목표
- `memo.php` : 메모
- `css/` : 스타일 파일
- `DATABASE` : DB 파일

