<?php
/*
(c) 2018 Yuki Tsuji

Myページ

使用する時は，データベース情報を変更して下さい．

投稿用のテーブルを使用して下さい．
*/

session_start();

header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

//ログインセッションの確認
require_once("./require_once/login_session.php");

//データベース接続
require_once("./require_once/post_database.php");

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//CSRF対策
require_once("./require_once/csrf.php");

//XSS対策関数
function h($s){
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

//MySQLのセッションと同じ行数をカウント
$sql = "SELECT COUNT(*) FROM {$table_name} WHERE Id = '".mysqli_real_escape_string($link, $_SESSION['ID'])."'";
$res = mysqli_query($link, $sql);
$row = mysqli_fetch_array($res);
$data_count = $row[0];

//MySQLの全データを参照
$quryset = mysqli_query($link, "SELECT * FROM {$table_name} WHERE Id = '".mysqli_real_escape_string($link, $_SESSION['ID'])."' ORDER BY Comment_number DESC LIMIT 100");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="robots" content="noindex, nofollow, noarchive">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="./css/coils.css" rel="stylesheet">
<title>Myページ</title>
</head>
<body>
<div class="foo">
<header>
<p>ログイン中:<u><?php echo h($_SESSION['NAME']);?></u></p>
<form action="menu.php" method="POST">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<input type="submit" value="メニュー">
</form>
</header>
<h1>Myページ</h1>
<div style="text-align: center;">
<fieldset>
<p>あなたのユーザー情報</p>
<p>名前:<u><?php echo h($_SESSION['NAME']);?></u></p>
<p>ID:<u><?php echo h($_SESSION['ID']);?></u></p>
</fieldset>
</div>
<h2>My投稿一覧（最新100件）</h2>
<?php if($data_count > 0):?>
	<?php while($data = mysqli_fetch_array($quryset)):?>
		<?php if($data[1] === $_SESSION['NAME'] && $data[6] === $_SESSION['ID'] && $data[4] === "画像"):?>
			<span style="color: #888888;"><?php echo h($data[0]);?></span> <span style="color: #555555;"><?php echo h($data[1]);?></span> <span style="color: #888888;">@<?php echo h($data[6]);?></span><br />
			<?php echo nl2br(h($data[2]));?><br />
			<?php echo "<img src=$data[5] alt=\"表示できません．\" width=\"800px\" height=\"600px\">";?><br />
			<div style="text-align: right;"><span style="color: #888888;"><?php echo h($data[3]);?></span></div>
			<hr />
		<?php elseif($data[1] === $_SESSION['NAME'] && $data[6] === $_SESSION['ID'] && $data[4] == ""):?>
			<span style="color: #888888;"><?php echo h($data[0]);?></span> <span style="color: #555555;"><?php echo h($data[1]);?></span> <span style="color: #888888;">@<?php echo h($data[6]);?></span><br />
			<?php echo nl2br(h($data[2]));?><br />
			<div style="text-align: right;"><span style="color: #888888;"><?php echo h($data[3]);?></span></div>
			<hr />
		<?php endif;?>
	<?php endwhile;?>
<?php else:?>
	投稿がありません．<br />
	<br />
	<hr />
<?php endif;?>
<footer>
<div class="copyright">
<!-- (c) 2018 Yuki Tsuji -->
&#169; 2018 Yuki Tsuji
</div>
</footer>
</div>
</body>
</html>
