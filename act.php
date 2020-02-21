<?php
include_once 'db.inc.php';
if (isset($_REQUEST['op'])) $_REQUEST['op']();

function qshow0(){
	$mode = ['id', 'RAND()'];
	$n = $_REQUEST['mode'];
	echoJsn("SELECT * FROM `question` ORDER BY $mode[$n] LIMIT $_REQUEST[qn]");
}
function qshow(){
	global $pdo;
	$mode = ['id', 'RAND()'];
	$n = $_REQUEST['mode'];
	$ansKey = array('ans1', 'ans2', 'ans3', 'ans4', 'ans5');
	$rs = $pdo->query("SELECT * FROM `question` ORDER BY $mode[$n] LIMIT $_REQUEST[qn]")->fetchAll(PDO::FETCH_OBJ);
	foreach($rs as $key=>$exam){
		$ansRdm = array();
		$ansNdx = array();
		foreach($ansKey as $k=>$v){
			if($exam->$v){
				$ansRdm[] = $exam->$v;	// 建立答案初始陣列
				$ansNdx[] = $k;	// 建立答案序號初始陣列
			}
		}
		shuffle($ansNdx);	// 亂數排列答案序號
		foreach($ansNdx as $i=>$a){
			$t = $ansKey[$i];
			$rs[$key]->$t = $ansRdm[$a];	// 將亂數排列之後的答案寫回$rs
		}
	}
	$jsonData = array('total'=>count($rs), 'rows'=>$rs);
	header('Content-type: application/json');
	echo json_encode($jsonData);
	// 還要把亂數排列之後的「題號」與「答案序」寫回資料庫, 日後才能重現考卷及查詢
}