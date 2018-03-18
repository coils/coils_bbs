<?php
/*
(c) 2018 Yuki Tsuji

投稿用テーブルの作成

使用する時は，データベース情報を変更して下さい．
*/

session_start();

header('Expires:-1');
header('Cache-Control:');
header('Pragma:');

//データベース接続
require_once("./require_once/post_database.php");

//投稿用テーブルを作成
$sql = "CREATE TABLE {$table_name} (
Comment_number INT,
User VARCHAR(30),
Comment VARCHAR(255),
Time VARCHAR(30),
File_type VARCHAR(30),
File_path VARCHAR(255),
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
