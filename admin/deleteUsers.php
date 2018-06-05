<?php

require_once('../functions.php');

if(empty($_GET['id'])) {
    exit ('请传入正确参数');
}
$data_arr = explode(',',$_GET['id']);
foreach ($data_arr as $value) {
    if(!is_numeric($value)) continue;
    xiu_execute("delete from users where id = {$value};");
}
header('Location: /admin/users.php');