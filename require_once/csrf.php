<?php
/*
(c) 2018 Yuki Tsuji

CSRF対策
*/

//トークンをセッションにセットする関数
function settoken(){
	//トークン生成
	$token = rtrim(base64_encode(openssl_random_pseudo_bytes(32)), '=');
	$_SESSION['token'] = $token;
}

//トークンをセッションから取得する関数
function checktoken(){
	//セッションが空か生成したトークンと違うトークンでPOSTされた時
	if(empty($_SESSION['token']) || ($_SESSION['token'] !== $_POST['token'])){
		echo '不正なアクセスです．';
		exit();
	}
}

//GETの時
if($_SERVER['REQUEST_METHOD'] != 'POST'){
	settoken();
}

//POSTの時
else{
	checktoken();
}
?>
