<?php

require_once('../functions.php');

if(empty($_GET['id'])) {
    exit('请传入正确的参数');
}
$data_arr = explode(',',$_GET['id']);
foreach($data_arr as $value) {
    if(!is_numeric($value)) continue;
    xiu_execute("delete from posts where id = {$value};");
}
header('Location: '.$_SERVER['HTTP_REFERER']);