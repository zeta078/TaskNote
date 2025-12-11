<!-- logout.php -->
<?php
session_start();
session_unset();
session_destroy();
//header("Location: main.php");
echo "<script>alert('로그아웃 되었습니다'); location.href='login.php';</script>";
exit;
?>
