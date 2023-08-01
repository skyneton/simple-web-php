<?php
require_once("../db.php");
require_once("../utils.php");

$comment_id = trim_or_empty($_GET['id']);
$content = trim_or_empty($_POST["content"]);
if(strlen($comment_id) <= 0 || strlen($content) <= 0) {
    http_response_code(400);
    die;
}

$mysqli = db_connect();
create_table($mysqli, "comment", "id INTEGER PRIMARY KEY AUTO_INCREMENT, bid INTEGER, writter TEXT, content TEXT");

$stmt = $mysqli->stmt_init();
$stmt->prepare("SELECT * FROM comment WHERE id = ?;");
$stmt->bind_param("i", $board_id);
$stmt->execute();
$cursor = $stmt->get_result();
if(mysql_num_rows($cursor) >= 1) {
    $row = $cursor->fetch_assoc();
    if($_SESSION["uid"] === $row["writter"]) {
        $stmt->close();
        $stmt = $mysqli->stmt_init();
        $stmt->prepare("UPDATE comment SET content = ? WHERE id = $comment_id");
        $stmt->bind_param("s", $content);
        $stmt->execute();
        $stmt->close();
        exit;
    }
}
$stmt->close();
$mysqli->close();
http_response_code(400);