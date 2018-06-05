<?php
require_once '../functions.php';

if(empty($_GET['id'])) {
    exit('请传入正确的参数');
}
$id = explode(',',$_GET['id']);
foreach($id as $value) {
    if(!is_numeric($value)) continue ;
    xiu_execute("delete from categories where id={$value};");
} 
 header('Location: /admin/categories.php');
