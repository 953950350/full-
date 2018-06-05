<?php
require_once '../functions.php';

//判断用户是否登录
 $user = xiu_get_current_user();

 function updata_password() {
   global $user;
   $GLOBALS['success'] = false;
   if(empty($_POST['old_password'])) {
     $GLOBALS['message'] = '请输入旧密码';
     return false;
   }
   $GLOBALS['old_password'] = $_POST['old_password'];
   if(empty($_POST['new_password'])) {
    $GLOBALS['message'] = '请输入新密码';
    return false;
  }
  $GLOBALS['new_password'] = $_POST['new_password'];
  if(empty($_POST['again_password'])) {
    $GLOBALS['message'] = '请再次输入新密码';
    return false;
  }
  $GLOBALS['again_password'] = $_POST['again_password'];

  if($_POST['old_password'] !== $user['password']) {
    $GLOBALS['message'] = '旧密码输入错误';
    return false;
  }
  if($_POST['again_password'] !== $_POST['new_password']) {
    $GLOBALS['message'] = '两次密码不一致';
    return false;
  }
  $user['password'] = $GLOBALS['new_password'];
  $rows = xiu_execute("update users set password = '{$GLOBALS['new_password']}' where id = {$user['id']} ;");
  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows > 0 ? '修改成功' : '修改失败';
  if($rows > 0) {
    $_SESSION['current_login_user'] = $user;
   }
 }



 if($_SERVER['REQUEST_METHOD'] === 'POST') {
   updata_password();
 }
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Password reset &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
  <?php include 'inc/navbar.php' ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>修改密码</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($GLOBALS['message'])): ?>
      <?php if($GLOBALS['success']): ?>
      <div class="alert alert-success">
        <strong>正确！</strong><?php echo $GLOBALS['message'];?>
      </div>
      <?php else:?>
      <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $GLOBALS['message'];?>
      </div>
      <?php endif ?>
      <?php endif ?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <div class="form-group">
          <label for="old" class="col-sm-3 control-label">旧密码</label>
          <div class="col-sm-7">
            <input id="old" name="old_password" value="<?php echo isset($GLOBALS['old_password']) ? $GLOBALS['old_password'] : '';?>" class="form-control" type="password" placeholder="旧密码">
          </div>
        </div>
        <div class="form-group">
          <label for="password" class="col-sm-3 control-label">新密码</label>
          <div class="col-sm-7">
            <input id="password" name="new_password" value="<?php echo isset($GLOBALS['new_password']) ? $GLOBALS['new_password'] : '';?>" class="form-control" type="password" placeholder="新密码">
          </div>
        </div>
        <div class="form-group">
          <label for="confirm" class="col-sm-3 control-label">确认新密码</label>
          <div class="col-sm-7">
            <input id="confirm" name="again_password" value="<?php echo isset($GLOBALS['again_password']) ? $GLOBALS['again_password'] : '';?>" class="form-control" type="password" placeholder="确认新密码">
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-7">
            <button type="submit" class="btn btn-primary">修改密码</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
