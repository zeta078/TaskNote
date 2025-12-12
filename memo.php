<!-- memo.php -->
<?php
session_start();
$currentPage = 'memo';

// 1) 로그인 여부 확인
if (!isset($_SESSION["userid"])) {
    echo "<script>alert('로그인이 필요한 서비스입니다.'); location.href='login.php';</script>";
    exit;
}

$userid   = $_SESSION["userid"];
$username = $_SESSION["username"] ?? "";

// 2) DB 연결
$conn = mysqli_connect("localhost", "root", "", "phpproject_db");
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

$mode  = $_POST["mode"] ?? "";
$iMemo = isset($_POST["iMemo"]) ? intval($_POST["iMemo"]) : 0;

// ------------------ 새 메모 추가 ------------------
if ($mode === "add") {
    $sTitle   = trim($_POST["sTitle"] ?? "");
    $sContent = trim($_POST["sContent"] ?? "");

    if ($sTitle !== "" && $sContent !== "") {
        $id_esc      = mysqli_real_escape_string($conn, $userid);
        $title_esc   = mysqli_real_escape_string($conn, $sTitle);
        $content_esc = mysqli_real_escape_string($conn, $sContent);

        $qry = "
            INSERT INTO memo_tbl (sID, sTitle, sContent, regDate)
            VALUES ('$id_esc', '$title_esc', '$content_esc', NOW())
        ";
        mysqli_query($conn, $qry);
    }
}

// ------------------ 메모 삭제 ------------------
if ($mode === "delete" && $iMemo > 0) {
    $qry = "SELECT sID FROM memo_tbl WHERE iMemo = $iMemo";
    $rst = mysqli_query($conn, $qry);
    $row = mysqli_fetch_assoc($rst);

    if ($row && $row["sID"] === $userid) {
        mysqli_query($conn, "DELETE FROM memo_tbl WHERE iMemo = $iMemo");
    }
}

// ------------------ 내 메모 목록 조회 ------------------
$id_esc = mysqli_real_escape_string($conn, $userid);
$qry  = "
    SELECT iMemo, sTitle, sContent, regDate
    FROM memo_tbl
    WHERE sID = '$id_esc'
    ORDER BY regDate DESC
";
$rst = mysqli_query($conn, $qry);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>메모 관리</title>
   <link rel="stylesheet" href="css/common.css">
<link rel="stylesheet" href="css/memo.css">

</head>
<body>

<!-- 네비바 -->
<div class="navbar">
    <div class="logo">TaskNote</div>

    <div class="nav-center">
        <a href="main.php"
           class="nav-link <?php if($currentPage === 'home') echo 'active'; ?>">Home</a>
        <a href="todo.php"
           class="nav-link <?php if($currentPage === 'todo') echo 'active'; ?>">할 일 / 목표</a>
        <a href="memo.php"
           class="nav-link <?php if($currentPage === 'memo') echo 'active'; ?>">메모</a>
    </div>

    <div class="menu">
        <span><?php echo htmlspecialchars($username); ?>님 환영합니다!</span>
        <a href="logout.php" class="btn-logout">로그아웃</a>
    </div>
</div>

<div class="container">
    <h1>메모 관리</h1>
    <p>
        간단한 아이디어, 해야 할 일, 회의 기록 등을 자유롭게 메모해 둘 수 있습니다.
    </p>

    <a href="main.php" class="btn-main">← Home으로 돌아가기</a>

    <!-- 새 메모 작성 폼 -->
    <h3>새 메모 작성</h3>

    <form method="post" action="">
        <input type="hidden" name="mode" value="add">

        <table width="100%" style="border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <th style="width: 120px;">제목</th>
                <td style="padding : 8px; border:1px solid #ccc;">
                    <input type="text" name="sTitle" style="width:95%;" required
                        placeholder="메모 제목을 입력해주세요.">
                </td>
            </tr>
            <tr>
                <th>내용</th>
                <td style="padding: 8px; border: 1px solid #ccc;">
                    <textarea name="sContent" rows="4" required
                        placeholder="메모 내용을 작성해주세요."></textarea>
                </td>
            </tr>
        </table>

        <button type="submit" style="
            padding: 10px 18px;
            background:#2D4DC2;
            color:white;
            border-radius:6px;
            font-size:14px;
            cursor:pointer;
            border:none;
        ">
            메모 저장
        </button>
    </form>

    <hr>

    <!-- 메모 목록 -->
    <h3>나의 메모 목록</h3>

<div class="memo-list">
<?php if ($rst && mysqli_num_rows($rst) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($rst)): 
        $title   = $row["sTitle"] ?? "";
        $content = $row["sContent"] ?? "";
        $preview = mb_strimwidth($content, 0, 180, "...", "UTF-8");
    ?>
        <div class="memo-item">
            <div class="memo-title-row">
                <div class="memo-title">
                    <?php echo htmlspecialchars($title); ?>
                </div>
                <div class="memo-date">
                    작성일 <?php echo $row["regDate"]; ?>
                </div>
            </div>
            <div class="memo-body">
                <?php echo nl2br(htmlspecialchars($preview)); ?>
            </div>
            <div class="memo-actions">
                <form method="post" action=""
                      onsubmit="return confirm('이 메모를 삭제할까요?');"
                      style="display:inline;">
                    <input type="hidden" name="mode" value="delete">
                    <input type="hidden" name="iMemo" value="<?php echo $row["iMemo"]; ?>">
                    <button type="submit" class="btn-action btn-danger">삭제</button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>아직 작성된 메모가 없습니다.</p>
<?php endif; ?>
</div>
</body>
</html>

<?php mysqli_close($conn); ?>
