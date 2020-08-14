<?php
include_once 'db.inc.php';
if (isset($_REQUEST['op'])) $_REQUEST['op']();

function qshow0(){
	$mode = ['id', 'RAND()'];
	$n = $_REQUEST['mode'];
	echoJsn("SELECT * FROM `question` ORDER BY $mode[$n] LIMIT $_REQUEST[qn]");
}
function qshow1(){
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
function qshow(){
	// 依照預先編輯好的試卷顯示
	global $pdo;
	$rs = $pdo->query("SELECT * FROM `sheet` JOIN `question` USING(qid) WHERE eid=1 ORDER BY sn")->fetchAll(PDO::FETCH_OBJ);
	foreach($rs as $key=>$exam){
		$anso = [$exam->ans1, $exam->ans2, $exam->ans3, $exam->ans4];
		if($exam->ans5)	$anso[] = $exam->ans5;
		$ansn = ansodr($anso, $exam->ansOdr);
		// 將排列之後的答案寫回$rs
		$rs[$key]->ans1 = $ansn[0];
		$rs[$key]->ans2 = $ansn[1];
		$rs[$key]->ans3 = $ansn[2];
		$rs[$key]->ans4 = $ansn[3];
		if(array_key_exists(4, $ansn))	$rs[$key]->ans5 = $ansn[4];
	}
	$jsonData = array('total'=>count($rs), 'rows'=>$rs);
	header('Content-type: application/json');
	echo json_encode($jsonData);
}
function finish(){
	$ans = array_filter($_REQUEST, function($v){
		return $v != 'op';
	}, ARRAY_FILTER_USE_KEY);
	$str = '';
	foreach($ans as $k=>$v){
		$sn = intval(preg_replace('/ans_/', '', $k)) + 1;
		$str .= '(1 ,'.$sn.', '.$v.'),';
	}
	$sql = "INSERT INTO `score` (eid, sn, answer) VALUES ".rtrim($str, ',');
	indb($sql);
}