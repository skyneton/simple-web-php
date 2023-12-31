<?php
require_once("../db.php");
require_once("../utils.php");
session_start();

$board_id = trim_or_empty($_GET['id']);
$board_id = str_replace("'", "''", $board_id);

$mysqli = db_connect();
create_table($mysqli, "board", "id INTEGER PRIMARY KEY AUTO_INCREMENT, writter TEXT, title TEXT, body TEXT");

$stmt = $mysqli->stmt_init();
// $stmt->prepare("SELECT * FROM board WHERE id = ?;");
// $stmt->bind_param("i", $board_id);
// $stmt->execute();
// $cursor = $stmt->get_result();
$cursor = $mysqli->query("SELECT * FROM board WHERE id = '".$board_id."';");
$title = "";
$body = "";
if($cursor->num_rows >= 1) {
    $row = $cursor->fetch_assoc();
    if($row["writter"] === $_SESSION["uid"]) {
        $title = $row["title"];
        $body = $row["body"];
    }
}
$mysqli->close();
?>

<input type="text" class="input-title" placeholder="TITLE" value="<?=$title ?>" />
<hr />
<div class="body" contenteditable="true" style="min-height: 200px; border: 1px solid black;">
    <?=$body?>
</div>
<div>
    <input type="file" class="file-upload" multiple />
    <ul class="file-list">
    </ul>
</div>
<button class="finished">완료</button>
<script defer>
    const fileList = [];
    document.getElementsByClassName("file-upload")[0].onchange = e => {
        const parent = document.getElementsByClassName("file-list")[0];
        for(const file of e.target.files) {
            const li = document.createElement("li");
            li.innerText = file.name;
            parent.appendChild(li);
            fileList.push(file);
        }
    };
    document.getElementsByClassName("finished")[0].onclick = async e => {
        const title = document.getElementsByClassName("input-title")[0].value;
        const body = document.getElementsByClassName("body")[0];
        if(body.innerText.trim() == "" || title.trim() == "") {
            alert("내용을 입력하세요.");
            return;
        }
        const data = new FormData();
        data.append("title", title);
        data.append("body", body.innerHTML);
        for(let i = 0, end = fileList.length; i < end; i++) {
            const file = fileList[i];
            data.append(`files[]`, file, file.name);
        }
        const res = await fetch(<?php if(isset($board_id)) echo "\"/api/write.php?id=$board_id\""; else echo "\"/api/write.php\""; ?>, {
            body: data,
            method: "POST"
        });
        if(!res.ok) {
            alert("작성에 실패했습니다.");
            return;
        }
        alert("작성 완료");
        history.back();
    }
</script>
