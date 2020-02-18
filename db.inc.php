<?php
define('DB', 'exam');
define('DBUSER', 'root');
define('DBPWD', 'raySecure0');

date_default_timezone_set('Asia/Taipei');

$pdo = new PDO('mysql:host=localhost;charset=utf8;dbname='.DB, DBUSER, DBPWD);

function b2u($str){
	return iconv('big5', 'utf-8//IGNORE', $str);
}

//去除$_REQUEST[]資料前後端含有空白字元或全形空白字元
function preCheck($data){
	return trim(str_replace('　', '', $data));
}

function dbQry($sql){
	global $pdo;
	return $pdo->query($sql)->fetchAll(PDO::FETCH_OBJ);
}

// 送出json格式的查詢結果(含Total; 不分頁)
function echoJsn($sql){
	global $pdo;
	$rs = $pdo->query($sql)->fetchAll(PDO::FETCH_OBJ);
	$jsonData = array('total'=>count($rs), 'rows'=>$rs);
	header('Content-type: application/json');
	echo json_encode($jsonData);
}

// 合成新增sql語句, 前端表單欄位名稱需符合資料表欄位名稱. (未Check傳入值)
function sqlNewGen($r){
	$str = '';
	foreach($r as $k=>$v){
		$r[$k] = '`'.$v.'`';
		$str .= "'".$_REQUEST[$v]."',";
	}
	return '('.join(',', $r).') VALUES ('.rtrim($str, ',').')';
}

// 合成更新sql語句, 前端表單欄位名稱需符合資料表欄位名稱.
function sqlUdGen($r){
	$str = '';
	foreach($r as $v){
		if (isset($_REQUEST[$v])) $str .= "`$v`='".$_REQUEST[$v]."',";
	}
	return rtrim($str, ',');
}

function scandir2($dir){
	$out = array();
	if (is_dir($dir)){
		if ($dh = opendir($dir)){
			while (($file = readdir($dh)) !== false){
				if ($file == '.' or $file == '..') continue;
				if (!is_dir($dir . '/'. $file)) $out[] = b2u($file);
			}
			closedir($dh);
		}
	}
	sort($out);
	return $out;
}

function scandir1($dir){
	$out = array();
	if (is_dir($dir)){
		if ($dh = opendir($dir)){
			while (($file = readdir($dh)) !== false){
				if ($file == '.' or $file == '..') continue;
				if (!is_dir($dir . '/'. $file)) $out[] = b2u($file);
			}
			closedir($dh);
		}
	}
	sort($out);
	return $out;
}

function chgLang(){
	$_SESSION['lang'] = $_REQUEST['lang'];
}

function chkRequest($r){
	foreach($r as $v) $str .= (isset($_REQUEST[$v])) ? '`'.$v."`='".$_REQUEST[$v]."'," : '';
	return rtrim($str, ',');
}

function indb($sql){
	global $pdo;
	return ($pdo->exec($sql)) ? true : false;
}