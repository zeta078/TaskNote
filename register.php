<!-- register.php -->
<?php
// 회원가입 처리 코드 (POST)
$conn = mysqli_connect("localhost", "root", "", "phpproject_db");
$msg = "";  // 성공/실패 메시지 저장

if ($conn && isset($_POST["userid"])) {

    $userid   = $_POST["userid"];
    $username = $_POST["username"];
    $userpw   = $_POST["userpw"];
    $phone1   = $_POST["phone1"];
    $phone2   = $_POST["phone2"];
    $phone3   = $_POST["phone3"];
    $gender   = $_POST["gender"];
    $addr     = $_POST["addr"];

    // ① 아이디 중복 검사
    $checkSQL = "SELECT * FROM member_tbl WHERE userid='$userid'";
    $checkRST = mysqli_query($conn, $checkSQL);

    if ($checkRST && mysqli_num_rows($checkRST) > 0) {
        $msg = "duplicateID";  // 아이디 중복
    } else {

    // INSERT 실행
    $qry = "INSERT INTO member_tbl(userid, username, userpw, phone, gender, addr) VALUES(";
    $qry .= "'$userid', '$username', '$userpw', '$phone1-$phone2-$phone3', '$gender', '$addr')";
    $rst = mysqli_query($conn, $qry);

    if ($rst)
        $msg = "success";
    else
        $msg = "fail";
    }
}

if (is_resource($conn))
    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>회원가입</title>
<link rel="stylesheet" href="css/register.css">

</head>
<body>

<?php if ($msg === "success") { ?>
    <div class="container">
        <div class="success-box">
             회원가입이 완료되었습니다!<br><br>
            <a href="login.php">로그인하러 가기</a>
        </div>
    </div>
<?php exit(); } ?>

<div class="container">
    <div class="title">회원가입</div>

    <form method="post" oninput="validateForm()">

        <div class="inputBox">
            <label>아이디 (4자 이상)</label>
            <input type="text" name="userid" id="userid">
            <div id="idMsg" class="msg"></div>
        </div>

        <div class="inputBox">
            <label>성명</label>
            <input type="text" name="username" required>
        </div>

        <div class="inputBox">
            <label>비밀번호 (6자 이상)</label>
            <input type="password" name="userpw" id="userpw">
        </div>

        <div class="inputBox">
            <label>비밀번호 확인</label>
            <input type="password" id="pwCheck">
            <div id="pwMsg" class="msg"></div>
        </div>

        <div class="inputBox">
            <label>전화번호</label>
            <div style="display:flex; gap:5px;">
                <select name="phone1">
                    <option>010</option><option>011</option>
                    <option>016</option><option>017</option>
                    <option>018</option><option>019</option>
                </select>
                <input type="text" name="phone2" maxlength="4">
                <input type="text" name="phone3" maxlength="4">
            </div>
        </div>

        <div class="inputBox">
            <label>성별</label>
            <select name="gender">
                <option value="M">남성</option>
                <option value="F">여성</option>
            </select>
        </div>

        <div class="inputBox">
            <label>주소 (선택)</label>
            <input type="text" name="addr">
        </div>

        <button class="btn" id="joinBtn" disabled>회원가입</button>

        <?php if ($msg === "duplicateID") { ?>
        <div class="success-box" style="color:red;">
        !! 이미 존재하는 아이디입니다. !!<br>
        다른 아이디로 다시 시도해주세요.
        </div>
        <?php exit(); } ?>
    </form>
</div>

<script>
function validateForm(){
    let id = document.getElementById("userid").value;
    let pw = document.getElementById("userpw").value;
    let pwC = document.getElementById("pwCheck").value;

    let idMsg = document.getElementById("idMsg");
    let pwMsg = document.getElementById("pwMsg");
    let joinBtn = document.getElementById("joinBtn");

    let idOk = false, pwOk = false;

    // 아이디 유효성
    if(id.length >= 4){
        idMsg.innerHTML = "";
        idOk = true;
    } else {
        idMsg.innerHTML = "아이디는 4자 이상이어야 합니다.";
    }

    // 비밀번호 일치 확인
    if(pw.length >= 6 && pw === pwC){
        pwMsg.innerHTML = "";
        pwOk = true;
    } else {
        pwMsg.innerHTML = "비밀번호가 일치하지 않습니다.";
    }

    // 버튼 활성화
    if(idOk && pwOk){
        joinBtn.disabled = false;
        joinBtn.classList.add("enabled");
    } else {
        joinBtn.disabled = true;
        joinBtn.classList.remove("enabled");
    }
}
</script>

</body>
</html>
