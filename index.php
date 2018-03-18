<?php
/*
(c) 2018 Yuki Tsuji

Coils トップページ

使用する時は，データベース情報を変更して下さい．

アカウント用のテーブルを使用して下さい．
*/

session_start();

header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

//データベース接続
require_once("./require_once/account_database.php");

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//CSRF対策
require_once("./require_once/csrf.php");

//XSS対策関数
function h($s){
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

$errorMessage = "";

//ログインフォームからデータが送信された時
if(isset($_POST['login'])){
	
	//IDが空欄の時
	if(empty($_POST['id'])){
		$errorMessage = 'ID欄が未入力です．';
	}
	
	//パスワードが空欄の時
	elseif(empty($_POST['password'])){
		$errorMessage = 'パスワード欄が未入力です．';
	}
	
	//IDとパスワードが空欄でない時
	if(!empty($_POST['id']) && !empty($_POST['password'])){
		$id = $_POST['id'];
		$password = $_POST['password'];
		$quryset = mysqli_query($link, "SELECT * FROM {$table_name}");
		
		//MySQLのデータを1行ずつ見る
		while($data = mysqli_fetch_array($quryset)){
			
			//送信されたIDとパスワードが登録情報と一致する時
			if($id === $data[4] && password_verify($password, $data[2])){
				$_SESSION['NAME'] = $data[1];
				$_SESSION['ID'] = $data[4];
				header('Location: timeline.php');
				exit();
			}
			
			//送信されたIDとパスワードが登録情報と一致しない時
			else{
				$errorMessage = 'ID，あるいは，パスワードが間違ってます．';
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="author" content="Yuki Tsuji">
<meta name="description" content="Coilsは，画像のアップロード機能を備えた会員制掲示板です．">
<meta name="robots" content="noindex, nofollow, noarchive">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="./css/coils.css" rel="stylesheet">
<title>Coils</title>
</head>
<body>
<div class="foo">
<h1><span style="color: #FF0000;">C</span><span style="color: #FFFF00;">o</span><span style="color: #00CC00;">i</span><span style="color: #0000FF;">l</span><span style="color: #800080;">s</span></h1>
<fieldset>
<legend>Coilsへようこそ</legend>
<p>
Coilsは，画像のアップロード機能を備えた会員制掲示板です．
</p>
<div style="text-align: center;">
<form action="manual.php" name="manual" method="POST">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<a href="javascript:manual.submit()">説明と規約</a>
</form>
</div>
<br />
<div style="text-align: center;">
<form action="signup.php" name="signup" method="POST">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<a href="javascript:signup.submit()">新規登録</a>
</form>
</div>
</fieldset>
<br />
<fieldset>
<legend>ログインフォーム</legend>
<div style="text-align: center;">
<form action="" method="POST">
<div><span style="color: #FF0000;"><?php echo h($errorMessage);?></span></div>
ID<br /><input type="text" name="id" class="form" size="40" maxlength="20" value="<?php if(!empty($_POST['id'])){echo h($_POST['id']);}?>">
<br />
パスワード<br /><input type="password" name="password" class="form" size="40" maxlength="20" value="">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<br />
<br />
<input type="submit" name="login" value="ログイン">
</form>
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
