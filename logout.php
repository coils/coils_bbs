<?php
/*
(c) 2018 Yuki Tsuji

ログアウト
*/

session_start();

header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//XSS対策関数
function h($s){
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

//ログアウト処理とタイムアウト表示
if(isset($_SESSION['NAME']) && isset($_SESSION['ID'])){
	$Message = 'ログアウト';
	$errorMessage = 'ログアウトしました．';
}

else{
	$Message = 'タイムアウト';
	$errorMessage = 'セッションがタイムアウトしました．';
}

$_SESSION = array();

@session_destroy();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="robots" content="noindex, nofollow, noarchive">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="./css/coils.css" rel="stylesheet">
<title><?php echo h($Message);?></title>
</head>
<body>
<div class="foo">
<div style="text-align: center;">
<h1><?php echo h($Message);?></h1>
<br />

<div><?php echo h($errorMessage);?></div>

<br />
<button type="button" onClick="location.href='index.php'">OK</button><br />
</div>
<br />
<br />
<br />
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
