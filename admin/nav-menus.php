<?php
require_once '../functions.php';

//判断用户是否登录
 xiu_get_current_user();
 

 $nav_menus = xiu_fetch_all('select * from options where id=9;');
 $data = json_decode($nav_menus[0]['value'],true);

 function add_nav_link () {
   global $data;
   global $nav_menus;
   global $new_data;
   $GLOBALS['succes'] = false;
   if(empty($_POST['text'])) {
    $GLOBALS['message'] = '请填写文本';
    return;
   }
   $new_data = array();
   $new_data['text'] = $_POST['text'];
   if(empty($_POST['title'])) {
    $GLOBALS['message'] = '请填写标题';
    return;
   }
   $new_data['title'] = $_POST['title'];
   if(empty($_POST['href'])) {
    $GLOBALS['message'] = '请填写链接';
    return;
   }
   $new_data['link'] = $_POST['href'];
   $data[] = $new_data;
   $add_data = json_encode($data,JSON_UNESCAPED_UNICODE);
   $rows = xiu_execute("update options set value = '{$add_data}' where id = 9 ;");
   $GLOBALS['succes'] = $rows > 0 ;
   $GLOBALS['message'] = $rows > 0 ? '添加成功' : '添加失败';

 }
 if($_SERVER['REQUEST_METHOD'] === 'POST') {
   add_nav_link();
 }
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Navigation menus &laquo; Admin</title>
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
        <h1>导航菜单</h1>
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
          <form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <h2>添加新导航链接</h2>
            <div class="form-group">
              <label for="text">文本</label>
              <input id="text" class="form-control" name="text" value="<?php echo isset($new_data['text']) ? $new_data['text'] : '' ;?>" type="text" placeholder="文本">
            </div>
            <div class="form-group">
              <label for="title">标题</label>
              <input id="title" class="form-control" name="title" value="<?php echo isset($new_data['title']) ? $new_data['title'] : '' ;?>" type="text" placeholder="标题">
            </div>
            <div class="form-group">
              <label for="href">链接</label>
              <input id="href" class="form-control" name="href" value="<?php echo isset($new_data['link']) ? $new_data['link'] : '' ;?>" type="text" placeholder="链接">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" id="all_delete" href="/admin/deleteNavLink.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th>文本</th>
                <th>标题</th>
                <th>链接</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($data as $key => $value ): ?>
              <tr>
                <td class="text-center"><input type="checkbox" data-id = "<?php echo $key; ?>" class="check-items"></td>
                <td><?php echo $value['text'];?></td>
                <td><?php echo $value['title'];?></td>
                <td><?php echo $value['link'];?></td>
                <td class="text-center">
                  <a href="/admin/deleteNavLink.php?id=<?php echo $key ;?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'nav-menus' ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
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
</body>
</html>
