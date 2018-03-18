<?php
/*
(c) 2018 Yuki Tsuji

タイムライン

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

//ファイルの拡張子を取得する関数
function upload(){
	//ファイルの拡張子を取得
	$file_nm = $_FILES["upfile"]["name"];
	$tmp_ary = explode(".", $file_nm);
	$extension = $tmp_ary[count($tmp_ary)-1];
	
	//アップロードファイルが存在する時
	if(is_uploaded_file($_FILES["upfile"]["tmp_name"])){
		
		//拡張子がbmp，png，gif，jpgの時
		if($extension === "bmp" || $extension ===  "png" || $extension ===  "gif" || $extension === "jpg"){
			
			//filesフォルダにアップロードしたファイルの名前で移動
			if(move_uploaded_file($_FILES["upfile"]["tmp_name"], "./files/".$_FILES["upfile"]["name"])){
				chmod("./files/".$_FILES["upfile"]["name"], 0777);
			}
		}
	}
	
	return $extension;
}

//メッセージのリセット
$errorMessage = "";
$successfulMessage = "";

//'削除＆編集フォーム'ボタンが押された時
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_and_edit'])){
	$_SESSION['delete_and_edit'] = 'delete_and_edit';
}

//'フォームを元に戻す'ボタンが押された時
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['back_delete_and_edit'])){
	$_SESSION['delete_and_edit'] = "";
}

//投稿の時
if($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST['edit_mode']) && isset($_POST['submit_input'])){
	$user = $_SESSION['NAME'];
	$newline_count = substr_count($_POST['message'], "\r\n");
	
	//コメントが空欄の時
	if(empty($_POST['message'])){
		$errorMessage = 'コメント欄が未入力です．';
	}
	
	//改行文字数が10以上の時
	if($newline_count >= 10){
		$errorMessage = '改行数を10未満にして下さい．';
	}
	
	//改行文字数が10未満でコメントが空欄でない時
	elseif(!empty($_POST['message'])){
		$message = $_POST['message'];
		
		//投稿番号の初期値
		$Commnet_number = 1;
		
		//最新の投稿番号を取得
		$sql = "SELECT MAX(Comment_number) FROM {$table_name}";
		$res = mysqli_query($link, $sql);
		$row = mysqli_fetch_array($res);
		$Comment_number = $row[0];
		$Comment_number++;
		
		//投稿時間の取得
		$postedAt = date('Y/m/d H:i:s');
		
		//ID取得
		$id = $_SESSION['ID'];
		
		//IPアドレス取得
		$ip = $_SERVER['REMOTE_ADDR'];
		
		//timeセッションが存在しない時
		if(!$_SESSION['time']){
			$_SESSION['time'] = 1;
		}
		
		//timeセッションがtime関数未満の時
		if($_SESSION['time'] < time()){
			
			//アップロードファイルが存在する時
			if(is_uploaded_file($_FILES["upfile"]["tmp_name"])){
				
				//正規表現．Directory Traversal攻撃の文字が含まれていない時
				if(preg_match("/^[a-z0-9A-Z\-_]+\.[a-zA-Z]{3}$/", $_FILES["upfile"]["name"])){
					
					//同じ名前のアップロードファイルが存在しない時
					if(!file_exists("./files/".$_FILES["upfile"]["name"])){
						//アップロードファイルをfilesフォルダに保存．返り値は拡張子
						$extension = upload();
						
						//拡張子が画像ファイルの時
						if($extension === "bmp" || $extension ===  "png" || $extension ===  "gif" || $extension === "jpg"){
							$sql = "INSERT INTO {$table_name} (
							Comment_number,
							User,
							Comment,
							Time,
							File_type,
							File_path,
							Id,
							Ip
							) VALUES (
							'".mysqli_real_escape_string($link, $Comment_number)."',
							'".mysqli_real_escape_string($link, $user)."',
							'".mysqli_real_escape_string($link, $message)."',
							'".mysqli_real_escape_string($link, $postedAt)."',
							'".mysqli_real_escape_string($link, "画像")."',
							'".mysqli_real_escape_string($link, "./files/".$_FILES["upfile"]["name"])."',
							'".mysqli_real_escape_string($link, $id)."',
							'".mysqli_real_escape_string($link, $ip)."'
							)";
							
							mysqli_query($link, $sql);
							
							$successfulMessage = '投稿完了しました．';
							
							$time = time() + 30;
							$_SESSION['time'] = $time;
						}
						
						//拡張子が画像ファイルでない時
						else{
							$errorMessage = 'ファイルの拡張子は，bmp，png，gif，jpgのみ対応しています．';
						}
					}
					
					//同じ名前のアップロードファイルが存在する時
					else{
						$errorMessage = 'ファイル名を変更して下さい．';
					}
				}
				
				//正規表現．Directory Traversal攻撃の文字が含まれている時
				else{
					$errorMessage = 'ファイル名に使用できない文字が含まれてます．ファイル名は，英数字，ハイフン，アンダーバーのみ使用できます．';
				}
			}
			
			//アップロードファイルが存在しない時
			else{
				$sql = "INSERT INTO {$table_name} (
				Comment_number,
				User,
				Comment,
				Time,
				File_type,
				File_path,
				Id,
				Ip
				) VALUES (
				'".mysqli_real_escape_string($link, $Comment_number)."',
				'".mysqli_real_escape_string($link, $user)."',
				'".mysqli_real_escape_string($link, $message)."',
				'".mysqli_real_escape_string($link, $postedAt)."',
				'".mysqli_real_escape_string($link, "")."',
				'".mysqli_real_escape_string($link, "")."',
				'".mysqli_real_escape_string($link, $id)."',
				'".mysqli_real_escape_string($link, $ip)."'
				)";
				
				mysqli_query($link, $sql);
				
				$successfulMessage = '投稿完了しました．';
				
				$time = time() + 30;
				$_SESSION['time'] = $time;
			}
		}
		
		//timeセッションがtime関数未満でない時
		else{
			$errorMessage = '連続投稿できません．';
		}
	}
}

//削除の時
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_number']) && $_POST['delete_number'] > 0){
	//投稿を取得
	$quryset = mysqli_query($link, "SELECT * FROM {$table_name}");
	
	//MySQLのデータを1行ずつ見る
	while($data = mysqli_fetch_array($quryset)){
		
		//削除対象番号と同じ投稿番号の行を削除する
		if($_POST['delete_number'] === $data[0]){
			$sql = "DELETE FROM {$table_name} WHERE Comment_number = '".mysqli_real_escape_string($link, $data[0])."'";
			
			mysqli_query($link, $sql);
			
			$successfulMessage = '削除完了しました．';
		}
	}
}

//編集の時
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_number']) && $_POST['edit_number'] > 0){
	//投稿を取得
	$quryset = mysqli_query($link, "SELECT * FROM {$table_name}");
	
	//MySQLのデータを1行ずつ見る
	while($data = mysqli_fetch_array($quryset)){
		
		if($_POST['edit_number'] === $data[0]){
			//編集する項目の変数確保
			$edit_number = $data[0];
			$edit_user = $data[1];
			$edit_message = $data[2];
			
			$successfulMessage = '編集モード';
		}
	}
}

//投稿フォームが編集モードの時
if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['edit_mode'])){
	$edit_user = $_POST['user'];
	$newline_count = substr_count($_POST['message'], "\r\n");
	
	//名前が空欄の時
	if($edit_user == ""){
		$edit_user = '名無し';
	}
	
	//コメントが空欄の時
	if(empty($_POST['message'])){
		$errorMessage = 'コメント欄が未入力です．';
	}
	
	//改行文字数が10以上の時
	if($newline_count >= 10){
		$errorMessage = '改行数を10未満にして下さい．';
	}
	
	//改行文字数が10未満でコメントが空欄でない時
	elseif(!empty($_POST['message'])){
		$edit_message = $_POST['message'];
		$edit_number = $_POST['edit_mode'];
		
		//投稿時間の取得
		$postedAt = date('Y/m/d H:i:s');
		
		//ID取得
		$id = $_SESSION['ID'];
		
		//投稿を取得
		$quryset = mysqli_query($link, "SELECT * FROM {$table_name}");
		
		//timeセッションが存在しない時
		if(!@$_SESSION['time']){
			$_SESSION['time'] = 1;
		}
		
		//timeセッションがtime関数未満の時
		if($_SESSION['time'] < time()){
			
			//MySQLのデータを1行ずつ見る
			while($data = mysqli_fetch_array($quryset)){
				
				//MySQLをUPDATE
				if($edit_number == $data[0]){
					
					//アップロードファイルが存在する時
					if(is_uploaded_file($_FILES["upfile"]["tmp_name"])){
						
						//正規表現．Directory Traversal攻撃の文字が含まれていない時
						if(preg_match("/^[a-z0-9A-Z\-_]+\.[a-zA-Z]{3}$/", $_FILES["upfile"]["name"])){
							
							//同じ名前のアップロードファイルが存在しない時
							if(!file_exists("./files/".$_FILES["upfile"]["name"])){
								//アップロードファイルをfilesフォルダに保存．返り値は拡張子
								$extension = upload();
								
								//拡張子が画像ファイルの時
								if($extension === "bmp" || $extension ===  "png" || $extension ===  "gif" || $extension === "jpg"){
									$sql = "UPDATE {$table_name}
									 SET 
									User = '".mysqli_real_escape_string($link, $edit_user)."',
									Comment = '".mysqli_real_escape_string($link, $edit_message)."',
									Time = '".mysqli_real_escape_string($link, $postedAt)."',
									File_type = '".mysqli_real_escape_string($link, "画像")."',
									File_path = '".mysqli_real_escape_string($link, "./files/".$_FILES["upfile"]["name"])."'
									 WHERE 
									Comment_number = '".mysqli_real_escape_string($link, $edit_number)."'
									";
									
									mysqli_query($link, $sql);
									
									$successfulMessage = '編集完了しました．';
								}
								
								//拡張子が画像ファイルでない時
								else{
									$errorMessage = 'ファイルの拡張子は，bmp，png，gif，jpgのみ対応しています．';
								}
							}
							
							//同じ名前のアップロードファイルが存在する時
							else{
								$errorMessage = 'ファイル名を変更して下さい．';
							}
						}
						
						//正規表現．Directory Traversal攻撃の文字が含まれている時
						else{
							$errorMessage = 'ファイル名に使用できない文字が含まれてます．ファイル名は，英数字，ハイフン，アンダーバーのみ使用できます．';
						}
					}
					
					//アップロードファイルが存在しない時
					else{
						$sql = "UPDATE {$table_name}
						 SET 
						User = '".mysqli_real_escape_string($link, $edit_user)."',
						Comment = '".mysqli_real_escape_string($link, $edit_message)."',
						Time = '".mysqli_real_escape_string($link, $postedAt)."',
						File_type = '".mysqli_real_escape_string($link, "")."',
						File_path = '".mysqli_real_escape_string($link, "")."'
						 WHERE 
						Comment_number = '".mysqli_real_escape_string($link, $edit_number)."'
						";
						
						mysqli_query($link, $sql);
						
						$successfulMessage = '編集完了しました．';
					}
				}
			}
		}
		
		//timeセッションがtime関数未満でない時
		else{
			$errorMessage = '連続投稿できません．';
		}
	}
}

//MySQLの行数をカウント
$sql = "SELECT COUNT(*) FROM {$table_name}";
$res = mysqli_query($link, $sql);
$row = mysqli_fetch_array($res);
$data_count = $row[0];

//MySQLの全データを参照
$quryset = mysqli_query($link, "SELECT * FROM {$table_name} ORDER BY Comment_number DESC LIMIT 100");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="robots" content="noindex, nofollow, noarchive">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="./css/coils.css" rel="stylesheet">
<title>タイムライン</title>
</head>
<body>
<div class="foo">
<header>
<p>ログイン中:<u><?php echo h($_SESSION['NAME']);?></u></p>
<form action="logout.php" method="POST">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<input type="submit" name="logout" value="ログアウト">
</form>
</header>
<h1>タイムライン</h1>
<div style="text-align: center;">
<p>
<form action="menu.php" method="POST">
<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
<input type="submit" value="メニュー">
</form>
</p>
</div>
<h2>新規投稿</h2>
<fieldset>
<div style="text-align: center;">
<?php if($_SESSION['ID'] === 'Administrator'):?>
	<?php if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['delete_and_edit'] === 'delete_and_edit'):?>
		<form action="" method="POST">
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
		<input type="submit" name="back_delete_and_edit" value="フォームを元に戻す">
		</form>
	<?php else:?>
		<form action="" method="POST">
		<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
		<input type="submit" name="delete_and_edit" value="削除＆編集フォーム">
		</form>
	<?php endif;?>
<?php endif;?>
<form action="" method="POST" enctype="multipart/form-data">
<div><span style="color:#FF0000;"><?php echo h($errorMessage);?></span></div>
<div><span style="color:#0000FF;"><?php echo h($successfulMessage);?></span></div>
<?php if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_number']) && $_POST['edit_number'] > 0):?>
	名前<br /><input type="text" name="user" class="form" size="40" maxlength="10" value="<?php echo h($edit_user);?>" placeholder="未入力の場合「名無し」">
	<br />
	コメント <span style="color: #FF0000;">※必須</span><br /><textarea name="message" cols="70" rows="4" maxlength="200"><?php echo h($edit_message);?></textarea>
	<input type = "hidden" name = "edit_mode" value="<?php echo h($edit_number);?>">
	<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
	<br />
	アップロードファイル<br />
	<input type="file" name="upfile" class="form" size="45">
	<br />
	<br />
	<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
	<input type="submit" name="edit" value="編集">
<?php else:?>
	コメント <span style="color: #FF0000;">※必須</span><br /><textarea name="message" cols="70" rows="4" maxlength="200"></textarea>
	<input type="hidden" name="MAX_FILE_SIZE" value="3000000">
	<br />
	アップロードファイル<br />
	<input type="file" name="upfile" class="form" size="45">
	<br />
	<br />
	<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
	<input type="submit" name="submit_input" value="投稿">
<?php endif;?>
</form>
</div>
</fieldset>
<?php if($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['delete_and_edit'] === 'delete_and_edit'):?>
	<h2>投稿削除</h2>
	<fieldset>
	<div style="text-align: center;">
	<form action="" method="POST">
	削除対象番号<br /><input type="number" name="delete_number" class="form" size="40" maxlength="20">
	<br />
	<br />
	<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
	<input type="submit" name="submit_delete" value="削除">
	</form>
	</div>
	</fieldset>
	<h2>投稿編集</h2>
	<fieldset>
	<div style="text-align: center;">
	<form action="" method="POST">
	編集対象番号<br /><input type="number" name="edit_number" class="form" size="40" maxlength="20">
	<br />
	<br />
	<input type="hidden" name="token" value="<?php echo h($_SESSION['token']);?>">
	<input type="submit" name="submit_edit" value="編集">
	</form>
	</div>
	</fieldset>
<?php endif;?>
<h2>投稿一覧（最新100件）</h2>
<?php if($data_count > 0):?>
	<?php while($data = mysqli_fetch_array($quryset)):?>
		<?php if($data[4] === "画像"):?>
			<span style="color: #888888;"><?php echo h($data[0]);?></span> <span style="color: #555555;"><?php echo h($data[1]);?></span> <span style="color: #888888;">@<?php echo h($data[6]);?></span><br />
			<?php echo nl2br(h($data[2]));?><br />
			<?php echo "<img src=$data[5] alt=\"表示できません．\" width=\"800px\" height=\"600px\">";?><br />
			<div style="text-align: right;"><span style="color: #888888;"><?php echo h($data[3]);?></span></div>
			<hr />
		<?php elseif($data[4] == ""):?>
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
