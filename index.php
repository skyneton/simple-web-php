<?php
require_once("db.php");

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTOINCREMENT, writter TEXT, title TEXT, body TEXT");

if(isset($_GET['query'])) {
    $stmt = $mysqli->stmt_init();
    $stmt->prepare("SELECT id, title FROM board WHERE title LIKE '%' || ? || '%' OR body LIKE '%' || ? || '%' ORDER BY id DESC;");
    $stmt->bind_param($_GET['query'], $_GET['query']);
    $stmt->execute();
    $cursor = $stmt->get_result();
}else {
    $cursor = $mysqli->query("SELECT id, title FROM board ORDER BY id DESC;");
}
?>
<ul>
    <?if(isset($_SESSION["uid"])) {?>
        <li><a href="/user/logout.php">로그아웃</a></li>
    <?} else {?>
        <li><a href="/user/login.php">로그인</a></li>
        <li><a href="/user/register.php">회원가입</a></li>
    <?}?>
</ul>
<div>
    <input type="search" class="search-query" value="<?=str_replace($_GET['query'], "\"", "\\\"")?>" placeholder="검색"/>
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
    <?while($row = $cursor->fetch_row()) {?>
        <tr>
            <td><?=$row["id"]?></td>
            <td>
                <a href="/board/?id=<?=$row["id"]?>">
                <?=$row["title"]?>
                </a>
            </td>
        </tr>
    <?}?>
</table>
<?
$stmt->close();
$mysqli->close();
?>