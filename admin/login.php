<?php

//引入基本的配置信息
require_once '../config.php';
//开启session
session_start();

function login () {
  if(empty($_POST['email'])) {
    $GLOBALS['message'] = '请输入邮箱';
    return;
  }
  if(empty($_POST['password'])) {
    $GLOBALS['message'] = '请输入密码';
    return; 
  }
  $content = mysqli_connect(XIU_DB_HOST,XIU_DB_USER,XIU_DB_PASS,XIU_DB_NAME);

  if(!$content) {
    exit('<h1>连接数据库失败</h1>');
  }
  $query = mysqli_query($content,"select * from users where email = '{$_POST['email']}' limit 1 ;");

  if(!$query) {
    $GLOBALS['message'] = '查询失败请稍后再试';
    return; 
  }

  $data = mysqli_fetch_assoc($query);
  if(!$data) {
    $GLOBALS['message'] = '用户名不存在';
    return; 
  }
  if($data['password'] !== $_POST['password']) {
    $GLOBALS['message'] = '密码错误';
    return; 
  }

$_SESSION['current_login_user'] = $data;

header('Location: /admin');





}


if($_SERVER['REQUEST_METHOD'] === 'POST') {
  login();
}

//退出登录业务
if($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'login_out') {
 //删除任务标识
  unset($_SESSION['current_login_user']);
}

?>


<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/animate/animate.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
  <!-- 可以通过在form上添加novalidate取消浏览器自带的校验功能 -->
  <!-- 可以通过autocomplete关闭浏览器自动提示功能 -->
    <form class="login-wrap<?php echo isset($message)?' shake animated':''; ?>" name="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" novalidate autocomplete="off">
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if(isset($message)): ?>
      <div class="alert alert-danger">
        <strong><?php echo $message ?></strong> 
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus value="<?php echo isset($_POST['email'])?$_POST['email']:''; ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script>
    $(function ($) {
      $emailFromcat = /^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/;
      $('#email').on('blur',function () {
        var value = $(this).val();
        if(!value || !$emailFromcat.test(value)) return;
        $.get('/admin/api/avatar.php',{'email':this.value},function (res) {
          if(!res) return;
          $('.avatar').fadeOut(function () {
            $(this).on('load',function () {
              $(this).fadeIn();
            }).attr('src',res)
          });
        })
      })
    })
  </script>
</body>
</html>
