<?php
/*
(c) 2018 Yuki Tsuji

アカウント用テーブルの作成

使用する時は，データベース情報を変更して下さい．
*/

session_start();

header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

//データベース接続
require_once("./require_once/account_database.php");

//アカウント用テーブルを作成
$sql = "CREATE TABLE {$table_name} (
User_number INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
User VARCHAR(30),
Password VARCHAR(255),
Time VARCHAR(30),
Id VARCHAR(30),
Ip VARCHAR(30)
)";

//テーブルの作成に成功した時
if(mysqli_query($link, $sql)){
	echo 'Table created successfully';
}

//テーブルの作成に失敗した時
else{
	echo 'Error creating table';
}
?>
