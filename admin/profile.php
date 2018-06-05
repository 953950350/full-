<?php
require_once '../functions.php';

//判断用户是否登录
$user = xiu_get_current_user();


 function change_user() {
   global $user;
   $avatar = isset($_POST['avatar']) && $_POST['avatar'] ? $_POST['avatar'] : $user['avatar'];
   $user['avatar'] = $avatar;
   $slug = isset($_POST['slug']) && $_POST['slug'] ? $_POST['slug'] : $user['slug'];
   $user['slug'] = $slug;
   $nickname = isset($_POST['nickname']) && $_POST['nickname'] ? $_POST['nickname'] : $user['nickname'];
   $user['nickname'] = $nickname;
   $rows = xiu_execute("update users set avatar = '{$avatar}' , slug = '{$slug}' , nickname = '{$nickname}' where id = {$user['id']} ;");
   $GLOBALS['success'] = $rows > 0;
   $GLOBALS['message'] = $rows > 0 ? '修改成功' : '修改失败';
   if($rows > 0) {
    $_SESSION['current_login_user'] = $user;
   }

 }


 if($_SERVER['REQUEST_METHOD'] === 'POST') {
   change_user();
 }

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Dashboard &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
  <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>我的个人资料</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <div class="alert alert-danger" style="display: none">
        <strong>错误！发生XXX错误</strong>
      </div>
      <?php if(isset($GLOBALS['message'])): ?>
      <div class="alert alert-<?php echo $GLOBALS['success'] ? 'success' : 'danger';?>">
        <strong><?php echo $GLOBALS['success'] ? '修改成功' : '修改失败';?></strong>
      </div>
      <?php endif ?>
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" novalidate>
        <div class="form-group">
          <label class="col-sm-3 control-label">头像</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="avatar" type="file" accept="image/*">
              <img src="<?php echo $user['avatar']; ?>">
              <i class="mask fa fa-upload"></i>
              <input type="hidden" name="avatar" value="<?php echo $user['avatar']; ?>">
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="email" class="col-sm-3 control-label">邮箱</label>
          <div class="col-sm-6">
            <input id="email" class="form-control" name="email" type="type" value="<?php echo $user['email']; ?>" placeholder="邮箱" readonly>
            <p class="help-block">登录邮箱不允许修改</p>
          </div>
        </div>
        <div class="form-group">
          <label for="slug" class="col-sm-3 control-label">别名</label>
          <div class="col-sm-6">
            <input id="slug" class="form-control" name="slug" type="type" value="<?php echo $user['slug']; ?>" placeholder="slug">
            <p class="help-block">https://zce.me/author/<strong>zce</strong></p>
          </div>
        </div>
        <div class="form-group">
          <label for="nickname" class="col-sm-3 control-label">昵称</label>
          <div class="col-sm-6">
            <input id="nickname" class="form-control" name="nickname" type="type" value="<?php echo $user['nickname']; ?>" placeholder="昵称">
            <p class="help-block">限制在 2-16 个字符</p>
          </div>
        </div>
        <div class="form-group">
          <label for="bio" class="col-sm-3 control-label">简介</label>
          <div class="col-sm-6">
            <textarea id="bio" class="form-control" placeholder="Bio" cols="30" rows="6">MAKE IT BETTER!</textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-6">
            <button type="submit" class="btn btn-primary">更新</button>
            <a class="btn btn-link" href="password-reset.php">修改密码</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php include 'inc/sidebar.php' ; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    // 利用ajax实现异步文件上传
    $('#avatar').on('change',function () {
      // 1.获取选中图片
      var $this = $(this);
      var files = $(this).prop('files');
      if(!files.length) return;
      var file = files[0];
      var $danger = $('.alert-danger');
      if(file.type.indexOf('image/') < 0) {
        $danger.fadeIn().find('strong').html('文件格式错误');
        return;
      }
      if(file.size > 20*1024*1024) {
        $danger.fadeIn().find('strong').html('文件太大');
        return;
      }
      $danger.fadeOut()
      var data = new FormData();
      data.append('avatar',file);
      
      // 发送ajax请求
      var xhr = new XMLHttpRequest();
      xhr.open('POST','/admin/api/uploads.php');
      xhr.send(data);
      xhr.onload = function () {
        var imgSrc = this.responseText;
        $this.siblings('img').attr('src',imgSrc).siblings('[type="hidden"]').val(imgSrc);
      }
      
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
