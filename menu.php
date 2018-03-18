<?php
/*
(c) 2018 Yuki Tsuji

メニュー
*/

session_start();

header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

//ログインセッションの確認
require_once("./require_once/login_session.php");

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//CSRF対策
require_once("./require_once/csrf.php");

//XSS対策関数
function h($s){
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="robots" content="noindex, nofollow, noarchive">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="./css/coils.css" rel="stylesheet">
<title>メニュー</title>
</head>
<body>
<div class="foo">
<header>
<p>ログイン中:<u><?php echo h($_SESSION['NAME']);?></u></p>
<form action="timeline.php" method="POST">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<input type="submit" value="タイムライン">
</form>
</header>
<h1>メニュー</h1>
<fieldset>
<div style="text-align: center;">
<p>
<form action="my_page.php" method="POST">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<input type="submit" value="Myページ"><br />
</form>
</p>
</div>
<div style="text-align: center;">
<p>
<form action="my_log.php" method="POST">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<input type="submit" value="My全投稿"><br />
</form>
</p>
</div>
<div style="text-align: center;">
<p>
<form action="log.php" method="POST">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<input type="submit" value="全投稿"><br />
</form>
</p>
</div>
</fieldset>
<br />
<footer>
<hr />
<div class="copyright">
<!-- (c) 2018 Yuki Tsuji -->
&#169; 2018 Yuki Tsuji
</div>
</footer>
</div>
</body>
</html>
