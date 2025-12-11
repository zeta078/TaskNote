<!-- main.php -->
<?php
session_start();
$currentPage = 'home';

// DB는 "로그인 된 경우"에만 연결
$conn     = null;
$rstTodo  = null;
$rstMemo  = null;

if (isset($_SESSION["userid"])) {
    $conn = mysqli_connect("localhost", "root", "", "phpproject_db");

    if ($conn) {
        $userid = $_SESSION["userid"];
        $id_esc = mysqli_real_escape_string($conn, $userid);

        // ===== 최근 할 일 리스트는 최대 5개까지 출력 =====
        $qryTodo = "
            SELECT sTitle, dueDate, isDone
            FROM todo_tbl
            WHERE sID = '$id_esc'
            ORDER BY isDone ASC, regDate DESC
            LIMIT 5
        ";
        $rstTodo = mysqli_query($conn, $qryTodo);

        // ===== 최근 메모는 최대 5개까지 출력 =====
        $qryMemo = "
            SELECT iMemo, sTitle, sContent, regDate
            FROM memo_tbl
            WHERE sID = '$id_esc'
            ORDER BY regDate DESC
            LIMIT 5
        ";
        $rstMemo = mysqli_query($conn, $qryMemo);
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>메인 페이지</title>
<link rel="stylesheet" href="css/common.css">
<link rel="stylesheet" href="css/main.css">
</head>

<body>

<!-- 네비게이션 바 -->
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
        <?php if (!isset($_SESSION["userid"])) { ?>
            <a href="login.php" class="btn-login">로그인</a>
        <?php } else { ?>
            <span><?php echo htmlspecialchars($_SESSION['username']); ?>님 환영합니다!</span>
            <a href="logout.php" class="btn-logout">로그아웃</a>
        <?php } ?>
    </div>
</div>

<div class="container">

    <?php if (!isset($_SESSION["userid"])) { ?>
        <!-- 🔹 로그인 전: 안내 화면 -->
        <h1>TaskNote</h1>
        <p>
            이 사이트는 PHP를 이용한<br>
            <b>회원가입 · 로그인 · 세션 관리 · 간단한 홈페이지 기능</b>을 연습하기 위해 제작되었습니다.<br><br>
            오른쪽 상단의 <b>로그인</b> 버튼을 눌러 시작하세요.
        </p>

    <?php } else { ?>
        <!-- 🔹 로그인 후: 대시보드 요약 화면 -->
        <h1>할 일 & 메모 한눈에 보기</h1>
        <p>
             안녕하세요, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>님!<br><br>
            아래에서 <b>최근에 등록한 할 일/목표 or 메모장</b>을 한눈에 확인할 수 있습니다.<br>
            새로운 (할 일/목표 or 메모장)을 <b>추가</b>하거나, <b>수정·삭제</b>하려면
            상단 메뉴를 이용하거나,<br> 각각의 리스트에서 <b>전체 보기</b>를 눌러주세요.
        </p>

        <!-- ===== 최근 할 일 요약 ===== -->
        <hr class="section-divider">
        <h3>최근 등록한 할 일</h3>

        <?php
        if ($conn && $rstTodo && mysqli_num_rows($rstTodo) > 0) {
        ?>
            <table>
                <tr>
                    <th width="100">할 일</th>
                    <th width="120">마감일</th>
                    <th width="90">상태</th>
                </tr>
                <?php 
                    $today = date('Y-m-d');
                    while($row = mysqli_fetch_assoc($rstTodo)) {

                        $statusLabel = $row["isDone"] ? "완료" : "진행 중";
                        $statusClass = $row["isDone"] ? "badge badge-done" : "badge badge-doing";

                        $dueText  = "-";
                        $dueClass = "";
                        if (!empty($row["dueDate"])) {
                            $dueText = $row["dueDate"];

                            if ($row["dueDate"] < $today) {
                                $dueClass = "due-past";
                            } elseif ($row["dueDate"] == $today) {
                                $dueClass = "due-today";
                            }
                        }
                ?>
                    <tr>
                        <td style="text-align:left;">
                            <?php echo htmlspecialchars($row["sTitle"]); ?>
                        </td>
                        <td class="<?php echo $dueClass; ?>" style="text-align:center;">
                            <?php echo $dueText; ?>
                        </td>
                        <td style="text-align:center;">
                            <span class="<?php echo $statusClass; ?>">
                                <?php echo $statusLabel; ?>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <div class="sub-text" style="text-align:right;">
                전체 보기 → <a href="todo.php">할 일 / 목표</a>
            </div>
        <?php
        } else {
            echo "<p>아직 등록된 할 일이 없습니다.</p>";
        }
        ?>

        <!-- ===== 최근 메모 요약 (카드) ===== -->
        <hr class="section-divider">
        <h3>최근 메모</h3>

        <?php
        if ($conn && $rstMemo && mysqli_num_rows($rstMemo) > 0) {
            while($row = mysqli_fetch_assoc($rstMemo)) {
                $title   = $row["sTitle"] ?? "";
                $content = $row["sContent"] ?? "";
                $preview = mb_strimwidth($content, 0, 80, "...", "UTF-8");
        ?>
            <div class="memo-card">
                <div class="memo-title">
                    <?php echo htmlspecialchars($title); ?>
                </div>
                <div class="memo-content">
                    <?php echo nl2br(htmlspecialchars($preview)); ?>
                </div>
                <div class="memo-meta">
                    작성일 <?php echo $row["regDate"]; ?>
                </div>
            </div>
        <?php
            }
        ?>
            <div class="sub-text" style="text-align:right;">
                전체 보기 → <a href="memo.php">메모</a>
            </div>
        <?php
        } else {
            echo "<p>아직 작성된 메모가 없습니다.</p>";
        }
        ?>

    <?php } ?>

</div>
</body>
</html>

<?php
if ($conn) {
    mysqli_close($conn);
}
?>
