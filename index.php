<?php
require_once("db.php");
session_start();

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTO_INCREMENT, writter TEXT, title TEXT, body TEXT");

if(isset($_GET['query'])) {
    $stmt = $mysqli->stmt_init();
    $stmt->prepare("SELECT id, title FROM board WHERE title LIKE '%' || ? || '%' OR body LIKE '%' || ? || '%' ORDER BY id DESC;");
    $stmt->bind_param("ss", $_GET['query'], $_GET['query']);
    $stmt->execute();
    $cursor = $stmt->get_result();
}else {
    $cursor = $mysqli->query("SELECT id, title FROM board ORDER BY id DESC", MYSQLI_USE_RESULT);
}
?>
<ul>
    <?php if(isset($_SESSION["uid"])) {?>
        <li><a href="/user/logout.php">로그아웃</a></li>
    <?php } else {?>
        <li><a href="/user/login.php">로그인</a></li>
        <li><a href="/user/register.php">회원가입</a></li>
    <?php }?>
</ul>
<div>
    <input type="search" class="search-query" value="<?php if(isset($_GET['query'])) echo trim($_GET['query']); ?>" placeholder="검색"/>
    <button class="search-query-btn">검색</button>
</div>
<a href="/board/write.php">
    <button>글 작성</button>
</a>
<table>
    <tr>
        <td>ID</td>
        <td>TITLE</td>
    </tr>
    <?php while($row = $cursor->fetch_assoc()) { ?>
        <tr>
            <td><?=$row["id"]?></td>
            <td>
                <a href="/board/?id=<?=$row["id"]?>">
                <?=$row["title"]?>
                </a>
            </td>
        </tr>
    <?php }?>
</table>

<script>
    document.getElementsByClassName("search-query-btn")[0].onclick = e => {
        location.href = `?query=${document.getElementsByClassName("search-query")[0].value}`;
    }
</script>

<?php
if(isset($stmt)) $stmt->close();
$mysqli->close();
?>
