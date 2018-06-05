<?php
require_once '../functions.php';

if(empty($_GET['id'])) {
    exit('请传入正确的参数');
}
$id = explode(',',$_GET['id']);
foreach($id as $value) {
    if(!is_numeric($value)) continue ;
    $nav_menus = xiu_fetch_all('select * from options where id = 10;');
    $data = json_decode($nav_menus[0]['value'],true);
    unset($data[$value]);
    $delte_data = json_encode($data,JSON_UNESCAPED_UNICODE);
    $rows = xiu_execute("update options set value = '{$delte_data}' where id = 10 ;");
    if($rows > 0) {
        echo '操作成功';
    } else {
        echo '操作失败';
    }

} 
 header('Location: /admin/slides.php');
