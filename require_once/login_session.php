<?php
/*
(c) 2018 Yuki Tsuji

ログインセッションの確認
*/

//ログインセッションの確認
if(!isset($_SESSION['NAME']) && !isset($_SESSION['ID'])){
	header('Location: logout.php');
	exit();
}
?>
