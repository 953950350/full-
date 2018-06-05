<?php
require_once '../functions.php';

//判断用户是否登录
 xiu_get_current_user();

 $slides = xiu_fetch_all('select * from options where id=10;');
 $data = json_decode($slides[0]['value'],true);

 function add_nav_link () {
   global $data;
   global $slides;
   global $new_data;
   $GLOBALS['succes'] = false;
   if(empty($_POST['image'])) {
    $GLOBALS['message'] = '请上传图片';
    return;
   }
   $new_data = array();
   $new_data['image'] = $_POST['image'];
   if(empty($_POST['text'])) {
    $GLOBALS['message'] = '请填写文本';
    return;
   }
   $new_data['text'] = $_POST['text'];
   if(empty($_POST['link'])) {
    $GLOBALS['message'] = '请填写链接';
    return;
   }
   $new_data['link'] = $_POST['link'];
   $data[] = $new_data;
   $add_data = json_encode($data,JSON_UNESCAPED_UNICODE);
   $rows = xiu_execute("update options set value = '{$add_data}' where id = 10 ;");
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
  <title>Slides &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
  <?php include('inc/navbar.php') ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>图片轮播</h1>
      </div>
      <!-- 上传图片的错误提示 -->
      <div class="alert alert-danger img-error" style="display: none">
        <strong>错误！</strong>
      </div>
      <!-- 提交表单的错误提示 -->
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
            <h2>添加新轮播内容</h2>
            <div class="form-group">
              <label for="image">图片</label>
              <!-- show when image chose -->
              <?php if (isset($new_data['image'])): ?>
              <img class="help-block thumbnail old_image" style="display: block" src="<?php echo $new_data['image'] ?>">
              <?php endif ?>
              <img class="help-block thumbnail" style="display: none">
              <input id="img_file" class="form-control" name="img_file" type="file">
              <input type="hidden" name="image" value="<?php echo isset($new_data['image']) ? $new_data['image'] : '' ;?>" >
            </div>
            <div class="form-group">
              <label for="text">文本</label>
              <input id="text" class="form-control" value="<?php echo isset($new_data['text']) ? $new_data['text'] : '' ;?>" name="text" type="text" placeholder="文本">
            </div>
            <div class="form-group">
              <label for="link">链接</label>
              <input id="link" class="form-control" value="<?php echo isset($new_data['link']) ? $new_data['link'] : '' ;?>" name="link" type="text" placeholder="链接">
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">添加</button>
            </div>
          </form>
        </div>
        <div class="col-md-8">
          <div class="page-action">
            <!-- show when multiple checked -->
            <a class="btn btn-danger btn-sm" id="all_delete" href="/admin/deleteSlides.php" style="display: none">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox"></th>
                <th class="text-center">图片</th>
                <th>文本</th>
                <th>链接</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($data as $key => $value ): ?>
              <tr>
              <td class="text-center"><input type="checkbox" data-id = "<?php echo $key; ?>" class="check-items"></td>
                <td class="text-center"><img class="slide" src="<?php echo $value['image'] ?>"></td>
                <td><?php echo $value['text'] ?></td>
                <td><?php echo $value['link'] ?></td>
                <td class="text-center">
                  <a href="/admin/deleteSlides.php?id=<?php echo $key ;?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'slides' ?>
  <?php include('inc/sidebar.php'); ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>
   // 选中图片时预览图片
   $('[name="img_file"]').on('change',function () {
      var files = $(this).prop('files');
      if(!files.length) {
        return;
      }
      var $this = $(this);
      var file = files[0];
      var $danger = $('.img-error');
      if(file.type.indexOf('image/') < 0) {
        $danger.fadeIn().find('strong').html('请上传图片文件');
        return;
      }
      if(file.size > 20*1024*1024) {
        $danger.fadeIn().find('strong').html('文件过大');
        return;
      }
      if(!$('.old_image')) {
        $('.old_image').hidden();
      }
      $danger.fadeOut();
      var data = new FormData();
      data.append('avatar',file);
      var xhr = new XMLHttpRequest();
      xhr.open('POST','/admin/api/uploads.php');
      xhr.send(data);
      xhr.onload = function () {
        var imgSrc = this.responseText;
        $this.siblings('img').attr('src',imgSrc).show().siblings('[name="image"]').val(imgSrc);
      }

    })
    // 删除功能
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
  </script>
  <script>NProgress.done()</script>
</body>
</html>
