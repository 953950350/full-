<?php

require_once '../../functions.php';
$result = 0;
if(empty($_GET['id'])) {
    echo $result;
    exit('请传入正确参数');
}
$data = explode(',',$_GET['id']);
if(isset($_GET['operate']) && $_GET['operate'] == '1') {
    foreach($data as $value) {
        $result += xiu_execute ("update comments set status = 'approved' where id = {$value};");
    }
    echo $result;
    exit();
}
if(isset($_GET['operate']) && $_GET['operate'] == '0') {
    foreach($data as $value) {
        $result += xiu_execute ("update comments set status = 'rejected' where id = {$value};");
    }
    echo $result;
    exit();
}

