<?php
include_once 'db.inc.php';
if (isset($_REQUEST['op'])) $_REQUEST['op']();

function qshow(){
	$mode = ['id', 'RAND()'];
	$n = $_REQUEST['mode'];
	echoJsn("SELECT * FROM `question` ORDER BY $mode[$n] LIMIT $_REQUEST[qn]");
}