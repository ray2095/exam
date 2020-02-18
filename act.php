<?php
include_once 'db.inc.php';
if (isset($_REQUEST['op'])) $_REQUEST['op']();

function qshow(){
    echoJsn("SELECT * FROM `question`");
}