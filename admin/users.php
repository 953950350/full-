<?php
  require_once '../functions.php';

//判断用户是否登录
  xiu_get_current_user();

  function add_user() {
    if(empty($_POST['email'])) {
      $GLOBALS['message'] = '请输入邮箱';
      $GLOBALS['succes'] = false;
      return;
    }
    if(empty($_POST['slug'])) {
      $GLOBALS['message'] = '请输入别名';
      $GLOBALS['succes'] = false;
      return;
    }
    if(empty($_POST['nickname'])) {
      $GLOBALS['message'] = '请输入昵称';
      $GLOBALS['succes'] = false;
      return;
    }
    if(empty($_POST['password'])) {
      $GLOBALS['message'] = '请输入密码';
      $GLOBALS['succes'] = false;
      return;
    }
    if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
      $GLOBALS['message'] = '邮箱地址错误';
      $GLOBALS['succes'] = false;
      return;
    }
    $email = $_POST['email'];
    $slug = $_POST['slug'];
    $nickname = $_POST['nickname'];
    $password = $_POST['password'];
    $rows = xiu_execute("insert into users values (null,'{$slug}','{$email}','{$password}','{$nickname}','/static/uploads/avatar.jpg',null,'activated');");
    $GLOBALS['succes'] = $rows > 0;
    $GLOBALS['message'] = $rows > 0 ? '添加成功' : '添加失败';
  }

  function change_user() {
    global $change_user_message;
    $slug = isset($_POST['slug']) && $_POST['slug'] ? $_POST['slug'] : $change_user_message['slug'];
    $change_user_message['slug'] = $slug;
    $email = isset($_POST['email']) && $_POST['email'] ? $_POST['email'] : $change_user_message['email'];
    $change_user_message['email'] = $email;
    $nickname = isset($_POST['nickname']) && $_POST['nickname'] ? $_POST['nickname'] : $change_user_message['nickname'];
    $change_user_message['nickname'] = $nickname;
    $password = isset($_POST['password']) && $_POST['password'] ? $_POST['password'] : $change_user_message['password'];
    $change_user_message['password'] = $password;
    if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
      $GLOBALS['message'] = '邮箱地址错误';
      $GLOBALS['succes'] = false;
      return;
    }
    $rows = xiu_execute("update users set nickname = '{$nickname}' , slug = '{$slug}' , email = '{$email}' , password = '{$password}' where id = {$_GET['id']};");
    $GLOBALS['succes'] = $rows > 0;
    $GLOBALS['message'] = $rows > 0 ? '修改成功': '修改失败';


  }


 if (isset($_GET['id'])) {
   $change_user_message = xiu_fetch_one("select * from users where id = {$_GET['id']}");
   if($_SERVER['REQUEST_METHOD'] === 'POST') {
    change_user();
  }

 } else {
  if($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_user();
  }
 }
 

$data = xiu_fetch_all('select * from users;');

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Users &laquo; Admin</title>
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
        <h1>用户</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <?php if(isset($message)): ?>
      <?php if($succes): ?>
      <div class="alert alert-success">
        <strong>正确！</strong><?php echo $message; ?>
      </div>
      <?php else: ?>
      <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message; ?>
      </div>
      <?php endif ?>
      <?php endif ?>
      <div class="row">
        <div class="col-md-4">
        <?php if(isset($change_user_message)): ?>
          <form action="<?php echo $_SERVER['PHP_SELF'].'?id='.$change_user_message['id']; ?>" method="post" novalidate>
            <h2>编辑<?php echo $change_user_message['nickname']; ?></h2>
            <div class="form-group">
              <label for="email"></label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱" value="<?php echo $change_user_message['email']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $change_user_message['slug']; ?>">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称" value="<?php echo $change_user_message['nickname']; ?>">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码" value="<?php echo $change_user_message['password']; ?>">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">保存</button>
            </div>
          </form>
        <?php else:?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" novalidate>
            <h2>添加新用户</h2>
            <div class="form-group">
              <label for="email">邮箱</label>
              <input id="email" class="form-control" name="email" type="email" placeholder="邮箱">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/author/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <label for="nickname">昵称</label>
              <input id="nickname" class="form-control" name="nickname" type="text" placeholder="昵称">
            </div>
            <div class="form-group">
              <label for="password">密码</label>
              <input id="password" class="form-control" name="password" type="text" placeholder="密码">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        <?php endif ?>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" id="all_delete" href="/admin/deleteUsers.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
               <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center" width="80">头像</th>
                <th>邮箱</th>
                <th>别名</th>
                <th>昵称</th>
                <th>状态</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach($data as $value ): ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id = "<?php echo $value['id']; ?>" class="check-items"></td>
                <td class="text-center"><img class="avatar" src="<?php echo $value['avatar']; ?>"></td>
                <td><?php echo $value['email']; ?></td>
                <td><?php echo $value['slug']; ?></td>
                <td><?php echo $value['nickname']; ?></td>
                <td><?php echo $value['status'] === 'activated'? '激活':'未激活'; ?></td>
                <td class="text-center">
                  <a href="/admin/users.php?id=<?php echo $value['id']; ?>" class="btn btn-default btn-xs">编辑</a>
                  <a href="/admin/deleteUsers.php?id=<?php echo $value['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'users' ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
    $(function ($) {
      var $checkbox = $('.check-items');
      var $btn = $('#all_delete');
      var dataArr = [];
      $checkbox.on('change',function () {
        if ($(this).prop('checked')) {
          dataArr.push($(this).data('id'));
        } else {
          dataArr.splice(dataArr.indexOf($(this).data('id')),1);
        }
        dataArr.length ? $btn.fadeIn() : $btn.fadeOut();
        $btn.prop('search','?id='+dataArr);
      })
    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
