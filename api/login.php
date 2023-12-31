<?php
require_once("../db.php");
require_once("../utils.php");
session_start();

if(isset($_SESSION["uid"])) {
    http_response_code(400);
    die;
}


$id = trim_or_empty($_POST["id"]);
$pw = trim_or_empty($_POST["pw"]);
if(strlen($id) <= 0 || strlen($pw) <= 0) {
    http_response_code(400);
    exit;
}

$mysqli = db_connect();
create_table($mysqli, "user", "id INTEGER AUTO_INCREMENT PRIMARY KEY, uid TEXT, pw TEXT");

$pw = hash("sha256", $pw);
$stmt = $mysqli->stmt_init();
// $stmt->prepare("SELECT * FROM user WHERE uid = ? AND pw = ?;");
// $stmt->bind_param("ss", $id, $pw);
// $stmt->execute();
// $cursor = $stmt->get_result();
$cursor = $mysqli->query("SELECT * FROM user WHERE uid = '".str_replace("'", "''", $id)."' AND pw = '".$pw."';");
if($cursor->num_rows >= 1) {
    $row = $cursor->fetch_assoc();
    $_SESSION["uid"] = $row["uid"];
    $stmt->close();
    $mysqli->close();
    exit;
}

$stmt->close();
$mysqli->close();
http_response_code(400);
exit;