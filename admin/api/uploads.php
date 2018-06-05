<?php
require_once('../../config.php');

if (empty($_FILES['avatar'])) {
    exit('必须上传文件');
}
$avatar = $_FILES['avatar'];
if ($avatar['error'] !== UPLOAD_ERR_OK) {
    exit('上传失败');
}
// 校验文件
if ($avatar['size'] > 20*1024*1024) {
    exit('图片过大');
}
$filetype = ['image/jpg','image/jpeg','image/gif','image/png'];
if (!in_array($avatar['type'],$filetype)) {
    exit('图片格式错误');
}
// 移动文件到网站范围之内
$ext = pathinfo($avatar['name'],PATHINFO_EXTENSION);
$target = '../../static/uploads/img'.uniqid().'.'.$ext;
if(!move_uploaded_file($avatar['tmp_name'],$target)) {
    exit('上传失败');
}
echo substr($target,5);
