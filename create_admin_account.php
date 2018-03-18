<?php
/*
(c) 2018 Yuki Tsuji

管理者用アカウントの作成

使用する時は，データベース情報を変更して下さい．

アカウント用のテーブルを使用して下さい．
*/

session_start();

header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

//データベース接続
require_once("./require_once/account_database.php");

//管理者アカウント作成
//'パスワード'を変更して下さい．
$name = '管理者';
$pass = 'パスワード';
$password = password_hash($pass, PASSWORD_DEFAULT);
$time = date('Y/m/d H:i:s');
$id = 'Administrator';
$ip = 'Administrator';

//MySQLの管理者IDと同じIDの行数をカウント
$sql = "SELECT COUNT(*) FROM {$table_name} WHERE Id = '".mysqli_real_escape_string($link, $id)."'";
$res = mysqli_query($link, $sql);
$row = mysqli_fetch_array($res);
$data_count = $row[0];

if($data_count > 0){
	echo '管理者アカウントが既に作成されてます．';
}

else{
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
	
	//管理者アカウント作成に成功した時
	if(mysqli_query($link, $sql)){
		echo '管理者アカウント作成に成功しました．';
	}
	
	//管理者アカウント作成に失敗した時
	else{
		echo '管理者アカウント作成に失敗しました．';
	}
}
?>
