<?php
    require_once '../../config.php';
    if(empty($_GET['email'])) {
        exit('缺少参数');
    }
    $connect = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);
    if(!$connect) {
        exit('数据库连接失败');
    }
    $query = mysqli_query($connect,"select avatar from users where email = '{$_GET['email']}' limit 1 ;");
    if(!$query) {
        exit('查询数据失败');
    }
    $data = mysqli_fetch_assoc($query);
    echo $data['avatar'];