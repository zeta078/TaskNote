<!-- login.php -->
<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "phpproject_db");

$loginFail = false;

if ($conn && isset($_POST["userid"])) {

    $userid = $_POST["userid"];
    $userpw = $_POST["userpw"];

    $qry = "SELECT * FROM member_tbl WHERE userid='$userid' AND userpw='$userpw'";
    $rst = mysqli_query($conn, $qry);

    if ($rst && mysqli_num_rows($rst) > 0) {
        $row = mysqli_fetch_assoc($rst);
        $_SESSION["userid"]   = $row["userid"];
        $_SESSION["username"] = $row["username"];
        header("Location: main.php");
        exit();
    } else {
        // 로그인 실패 → 아이디 존재 여부 확인
        $qry2    = "SELECT * FROM member_tbl WHERE userid='$userid'";
        $result2 = mysqli_query($conn, $qry2);

        if ($result2 && mysqli_num_rows($result2) == 0) {
            $loginFail = "noID";    // 아이디 존재하지 않음
        } else {
            $loginFail = "wrongPW"; // 비밀번호 틀림
        }
    }
}

if ($conn) {
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>로그인</title>
<link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="container">
    <div class="title">로그인</div>

    <?php if ($loginFail === "noID") { ?>
    <div class="msgFail"> !해당 계정은 존재하지 않습니다.!</div>
    <?php } else if ($loginFail === "wrongPW") { ?>
    <div class="msgFail"> !비밀번호가 올바르지 않습니다.!</div>
    <?php } ?>

    <form method="post">
        <div class="inputBox">
            <input type="text" name="userid" placeholder="아이디">
        </div>

        <div class="inputBox">
            <input type="password" name="userpw" placeholder="비밀번호">
        </div>

        <button class="btn">로그인</button>

        <div class="signupLink">
            처음이신가요? → <a href="register.php">회원가입</a>
        </div>
    </form>
</div>
</body>
</html>
