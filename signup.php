<?php
/*
(c) 2018 Yuki Tsuji

新規登録

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
$signupMessage = "";

//新規登録フォームからデータが送信された時
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])){
	
	//MySQLのPOST[id]と同じIDの行数をカウント
	$sql = "SELECT COUNT(*) FROM {$table_name} WHERE Id = '".mysqli_real_escape_string($link, $_POST['id'])."'";
	$res = mysqli_query($link, $sql);
	$row = mysqli_fetch_array($res);
	$data_count = $row[0];
	
	//MySQLに同じIDが存在する時
	if($data_count > 0){
		$errorMessage = 'そのIDは，既に使用されてます．';
	}
	
	//MySQLに同じIDが存在しない時
	else{
		//名前が空欄の時
		if(empty($_POST['name'])){
			$errorMessage = '名前欄が未入力です．';
		}
		
		//IDが空欄の時
		elseif(empty($_POST['id'])){
			$errorMessage = 'ID欄が未入力です．';
		}
		
		//パスワードが空欄の時
		elseif(empty($_POST['password'])){
			$errorMessage = 'パスワード欄が未入力です．';
		}
		
		//パスワード(確認)が空欄の時
		elseif(empty($_POST['password2'])){
			$errorMessage = 'パスワード(確認)欄が未入力です．';
		}
		
		//名前とIDとパスワードが空欄でなく，パスワードが一致する時
		if(!empty($_POST['name']) && !empty($_POST['id']) && !empty($_POST['password']) && !empty($_POST['password2']) && $_POST['password'] === $_POST['password2']){
			
			//正規表現．IDに英数字以外の文字が含まれていない時
			if(preg_match("/^[a-zA-Z0-9]+$/", $_POST['id'])){
				$name = $_POST['name'];
				$pass = $_POST['password'];
				$password = password_hash($pass, PASSWORD_DEFAULT);
				$time = date('Y/m/d H:i:s');
				$id = $_POST['id'];
				$ip = $_SERVER['REMOTE_ADDR'];
				
				$sql = "INSERT INTO {$table_name} (
				User,
				Password,
				Time,
				Id,
				Ip
				) VALUES (
				'".mysqli_real_escape_string($link, $name)."',
				'".mysqli_real_escape_string($link, $password)."',
				'".mysqli_real_escape_string($link, $time)."',
				'".mysqli_real_escape_string($link, $id)."',
				'".mysqli_real_escape_string($link, $ip)."'
				)";
				
				mysqli_query($link, $sql);
				
				$signupMessage = "新規登録が完了しました．登録情報は，名前:$name ID:$id になります．IDは，ログイン時に必要なので，覚えておいて下さい．";
			}
			
			//正規表現．IDに英数字以外の文字が含まれている時
			else{
				$errorMessage = 'IDに使用できない文字が含まれてます．IDは，英数字のみ使用できます．';
			}
		}
		
		//パスワードが一致しない時
		elseif($_POST['password'] != $_POST['password2']){
			$errorMessage = 'パスワードが一致しません．';
		}
	}
}

//MySQLの行数をカウント
$sql = "SELECT COUNT(*) FROM {$table_name}";
$res = mysqli_query($link, $sql);
$row = mysqli_fetch_array($res);
$data_count = $row[0];
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="robots" content="noindex, nofollow, noarchive">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="./css/coils.css" rel="stylesheet">
<title>新規登録</title>
</head>
<body>
<div class="foo">
<header>
<div>
登録ユーザー数（<?php echo h($data_count);?>人）
</div>
</header>
<h1>新規登録</h1>
<fieldset>
<legend>新規登録フォーム</legend>
<div style="text-align: center;">
<form action="" method="POST">
<div><span style="color: #FF0000;"><?php echo h($errorMessage);?></span></div>
<div><span style="color: #0000FF;"><?php echo h($signupMessage);?></span></div>
名前 <span style="color: #FF0000;">※必須</span><br /><input type="text" name="name" class="form" size="40" maxlength="10" value="<?php if(!empty($_POST['name'])){echo h($_POST['name']);}?>">
<br />
ID <span style="color: #FF0000;">※必須</span><br /><input type="text" name="id" class="form" size="40" maxlength="10" value="<?php if(!empty($_POST['id'])){echo h($_POST['id']);}?>">
<br />
パスワード <span style="color: #FF0000;">※必須</span><br /><input type="password" name="password" class="form" size="40" maxlength="20" value="">
<br />
パスワード(確認) <span style="color: #FF0000;">※必須</span><br /><input type="password" name="password2" class="form" size="40" maxlength="20" value="">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<br />
<br />
<input type="submit" name="signup" value="登録">
</form>
</div>
</fieldset>
<br />
<div style="text-align: center;">
<form action="/" name="toppage" method="POST">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<a href="javascript:toppage.submit()">トップページ</a>
</form>
</div>
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
