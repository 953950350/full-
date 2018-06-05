<?php

require_once '../../functions.php';

$result = 0;
if (empty($_GET['id'])) {
    exit($result);
}
$data_arr = explode(',',$_GET['id']);
foreach($data_arr as $value) {
    if(!is_numeric($value)) continue;
    $result += xiu_execute ("delete from comments where id = {$value};");
}

echo $result;