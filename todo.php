<!-- todo.php -->
<?php
session_start();
$currentPage = 'todo';

// 1) 로그인 여부 확인
if (!isset($_SESSION["userid"])) {
    echo "<script>alert('로그인이 필요한 서비스입니다.'); location.href='login.php';</script>";
    exit;
}

$userid   = $_SESSION["userid"];              // 로그인 ID
$username = $_SESSION["username"] ?? "";      // 표시용 이름

// 2) DB 연결
$conn = mysqli_connect("localhost", "root", "", "phpproject_db");
if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

$id_esc = mysqli_real_escape_string($conn, $userid);

// 3) mode 처리 (POST 전용)
$mode  = $_POST["mode"] ?? "";
$iTodo = isset($_POST["iTodo"]) ? intval($_POST["iTodo"]) : 0;

// 새 할 일 추가 
if ($mode === "add") {
    $sTitle   = trim($_POST["sTitle"] ?? "");
    $sContent = trim($_POST["sContent"] ?? "");
    $dueDate  = trim($_POST["dueDate"] ?? "");

    if ($sTitle !== "" && $dueDate !== "") {   // 할 일 + 마감일 필수
        $title_esc   = mysqli_real_escape_string($conn, $sTitle);
        $content_esc = mysqli_real_escape_string($conn, $sContent);
        $due_esc     = mysqli_real_escape_string($conn, $dueDate);

        $qry = "
            INSERT INTO todo_tbl (sID, sTitle, sContent, isDone, dueDate, regDate)
            VALUES ('$id_esc', '$title_esc', '$content_esc', 0, '$due_esc', NOW())
        ";
        mysqli_query($conn, $qry);
    }
}

// 기존 할 일 수정 
if ($mode === "update" && $iTodo > 0) {
    $sTitle   = trim($_POST["sTitle"] ?? "");
    $sContent = trim($_POST["sContent"] ?? "");
    $dueDate  = trim($_POST["dueDate"] ?? "");

    if ($sTitle !== "" && $dueDate !== "") {
        $title_esc   = mysqli_real_escape_string($conn, $sTitle);
        $content_esc = mysqli_real_escape_string($conn, $sContent);
        $due_esc     = mysqli_real_escape_string($conn, $dueDate);

        // 내가 쓴 할 일인지 확인 후 수정
        $qryCheck = "SELECT iTodo FROM todo_tbl WHERE iTodo=$iTodo AND sID='$id_esc'";
        $rstCheck = mysqli_query($conn, $qryCheck);
        if ($rstCheck && mysqli_num_rows($rstCheck) > 0) {
            $qryUpd = "
                UPDATE todo_tbl
                SET sTitle='$title_esc',
                    sContent='$content_esc',
                    dueDate='$due_esc'
                WHERE iTodo=$iTodo AND sID='$id_esc'
            ";
            mysqli_query($conn, $qryUpd);
        }
    }
}

// 완료/미완료 변경 
if ($mode === "toggle" && $iTodo > 0) {
    $qry = "SELECT sID, isDone FROM todo_tbl WHERE iTodo = $iTodo";
    $rst = mysqli_query($conn, $qry);
    $row = mysqli_fetch_assoc($rst);

    if ($row && $row["sID"] === $userid) {
        $newStatus = $row["isDone"] ? 0 : 1;
        mysqli_query($conn, "UPDATE todo_tbl SET isDone = $newStatus WHERE iTodo = $iTodo");
    }
}

// 할 일 삭제 
if ($mode === "delete" && $iTodo > 0) {
    $qry = "SELECT sID FROM todo_tbl WHERE iTodo = $iTodo";
    $rst = mysqli_query($conn, $qry);
    $row = mysqli_fetch_assoc($rst);

    if ($row && $row["sID"] === $userid) {
        mysqli_query($conn, "DELETE FROM todo_tbl WHERE iTodo = $iTodo");
    }
}

// 수정 버튼 눌렀을 때 → 위 입력폼에 값 채우기용 
$editId   = isset($_GET["edit"]) ? intval($_GET["edit"]) : 0;
$editTodo = null;

if ($editId > 0) {
    $qryEdit = "SELECT * FROM todo_tbl WHERE iTodo=$editId AND sID='$id_esc'";
    $rstEdit = mysqli_query($conn, $qryEdit);
    if ($rstEdit && mysqli_num_rows($rstEdit) > 0) {
        $editTodo = mysqli_fetch_assoc($rstEdit);
    }
}

// 내 할 일 목록 조회
$qry  = "
    SELECT iTodo, sTitle, sContent, isDone, dueDate, regDate
    FROM todo_tbl
    WHERE sID = '$id_esc'
    ORDER BY isDone ASC, regDate DESC
";
$rst = mysqli_query($conn, $qry);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>할 일 / 목표 관리</title>
    <link rel="stylesheet" href="css/common.css">
<link rel="stylesheet" href="css/todo.css">

</head>
<body>

<!-- 상단 네비바 --> 
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
        <a class="btn-logout" href="logout.php">로그아웃</a>
    </div>
</div>

<div class="container">
    <h1>할 일 / 목표 관리</h1>
    <p>
        <?php echo htmlspecialchars($username); ?>님이 실제로 수행해야 할 작업과 목표를 관리하는 페이지입니다.<br>
        <b>마감일</b>과 <b>진행 상태</b>를 관리하면서, 오늘 해야 할 일을 명확하게 정리해 보세요.
    </p>

    <a href="main.php" class="btn-main">← Home으로 돌아가기</a>

    <!-- 새 할 일 추가 / 수정 -->
    <?php $isEditing = ($editTodo !== null); ?>
    <h3><?php echo $isEditing ? "할 일 수정" : "새 할 일 추가"; ?></h3>

    <form method="post" action="">
        <input type="hidden" name="mode" value="<?php echo $isEditing ? 'update' : 'add'; ?>">
        <?php if ($isEditing): ?>
            <input type="hidden" name="iTodo" value="<?php echo $editTodo["iTodo"]; ?>">
        <?php endif; ?>

        <table width="100%" style="border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <th style="background:#f0f0f0; width: 120px; padding: 8px; border:1px solid #ccc;">
                    할 일 제목
                </th>
                <td style="padding: 8px; border:1px solid #ccc;">
                    <input type="text" name="sTitle" style="width: 95%;" required
                           placeholder="예) 등록할 할 일/목표"
                           value="<?php echo $isEditing ? htmlspecialchars($editTodo['sTitle']) : ''; ?>">
                </td>
            </tr>

            <tr>
                <th style="background:#f0f0f0; padding: 8px; border:1px solid #ccc;">
                    상세 내용
                </th>
                <td style="padding: 8px; border:1px solid #ccc;">
                    <textarea name="sContent" rows="4" style="width: 95%;"
                              placeholder="상세 내용을 작성해주세요."><?php
                        echo $isEditing ? htmlspecialchars($editTodo['sContent']) : '';
                    ?></textarea>
                </td>
            </tr>

            <tr>
                <th style="background:#f0f0f0; padding: 8px; border:1px solid #ccc;">
                    마감일 (필수)
                </th>
                <td style="padding: 8px; border:1px solid #ccc;">
                    <input type="date" name="dueDate" style="width: 50%;" required
                           value="<?php echo $isEditing ? htmlspecialchars($editTodo['dueDate']) : ''; ?>">
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
            <?php echo $isEditing ? "할 일 수정하기" : "할 일 추가하기"; ?>
        </button>
        <?php if ($isEditing): ?>
            <a href="todo.php" style="margin-left:8px; font-size:13px;">수정 취소</a>
        <?php endif; ?>
    </form>

    <hr>
  
<!-- 할 일 목록 -->
<h3>나의 할 일 목록</h3>
<?php if ($rst && mysqli_num_rows($rst) > 0): ?>
    <table class="list-table">
        <tr>
            <th width="100">할 일</th>
            <th>상세 내용</th>
            <th width="120">마감일</th>
            <th width="90">상태</th>
            <th width="160">관리</th>
        </tr>
        <?php 
            $today = date('Y-m-d');
            while ($row = mysqli_fetch_assoc($rst)): 

                // 상태 배지
                $statusLabel = $row["isDone"] ? "완료" : "진행 중";
                $statusClass = $row["isDone"] ? "badge badge-done" : "badge badge-doing";

                // 마감일 강조
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

                $content = $row["sContent"] ?? "";
                $preview = $content !== ""
                    ? mb_strimwidth($content, 0, 150, "...", "UTF-8")
                    : "";
                $id = (int)$row["iTodo"];
        ?>
            <!-- 메인 행 -->
            <tr class="<?php echo $row['isDone'] ? 'row-done' : ''; ?>">
                <td class="<?php echo $row["isDone"] ? 'done' : ''; ?>">
                    <?php echo htmlspecialchars($row["sTitle"]); ?>
                </td>

                <td>
                    <div class="todo-content-preview">
                        <?php
                            if ($preview !== "") {
                                echo nl2br(htmlspecialchars($preview));
                            } else {
                                echo "<span style='color:#999;'>상세 내용 없음</span>";
                            }
                        ?>
                    </div>
                </td>

                <td class="<?php echo $dueClass; ?>" style="text-align:center;">
                    <?php echo $dueText; ?>
                </td>

                <td style="text-align:center;">
                    <span class="<?php echo $statusClass; ?>">
                        <?php echo $statusLabel; ?>
                    </span>
                </td>
                <td class="action-cell">
                    <!-- 완료/미완료 토글 -->
                    <form method="post" action="">
                        <input type="hidden" name="mode" value="toggle">
                        <input type="hidden" name="iTodo" value="<?php echo $id; ?>">
                        <button type="submit" class="btn-action">
                            <?php echo $row["isDone"] ? "미완료로" : "완료로"; ?>
                        </button>
                    </form>

                    <!-- 상세 펼치기 -->
                    <button type="button"
                            class="btn-action"
                            onclick="toggleDetail(<?php echo $id; ?>);">
                        상세
                    </button>

                    <!-- 수정 (GET edit 파라미터) -->
                    <a href="todo.php?edit=<?php echo $id; ?>" class="btn-action">
                        수정
                    </a>

                    <!-- 삭제 -->
                    <form method="post" action=""
                          onsubmit="return confirm('정말 삭제하시겠습니까?');">
                        <input type="hidden" name="mode" value="delete">
                        <input type="hidden" name="iTodo" value="<?php echo $id; ?>">
                        <button type="submit" class="btn-action btn-danger">
                            삭제
                        </button>
                    </form>
                </td>
            </tr>

            <!-- 펼쳐지는 상세 행 -->
            <tr id="detail-<?php echo $id; ?>" class="detail-row">
                <td colspan="5" class="detail-cell">
                    <div class="detail-content">
                        <?php
                        if (trim($content) !== "") {
                            echo nl2br(htmlspecialchars($content));
                        } else {
                            echo "등록된 상세 내용이 없습니다.";
                        }
                        ?>
                    </div>
                </td>
            </tr>

        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>아직 등록된 할 일이 없습니다. 위에서 오늘 할 일을 추가해 보세요.</p>
<?php endif; ?>


</div>

<script>
function toggleDetail(id) {
    const row = document.getElementById('detail-' + id);
    if (!row) return;
    row.classList.toggle('open');
}
</script>

</body>
</html>

<?php mysqli_close($conn); ?>
