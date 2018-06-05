<?php
require_once '../functions.php';

//判断用户是否登录
 xiu_get_current_user();
 
 $data = xiu_fetch_all('select * from options WHERE id > 1 and id < 9 ;');

 function update_settings () {
  global $data;
  global $new_data;
  $GLOBALS['succes'] = false;
  if(empty($_POST['site_logo'])) {
   $GLOBALS['message'] = '请上传站点图标';
   return;
  }
  $data[0]['value'] = $_POST['site_logo'];
  if(empty($_POST['site_name'])) {
   $GLOBALS['message'] = '请填写站点名称';
   return;
  }
  $data[1]['value'] = $_POST['site_name'];
  if(empty($_POST['site_description'])) {
   $GLOBALS['message'] = '请填写站点描述';
   return;
  }
  $data[2]['value'] = $_POST['site_description'];
  if(empty($_POST['site_keywords'])) {
    $GLOBALS['message'] = '请填写站点关键词';
    return;
  }
  $data[3]['value'] = $_POST['site_keywords'];
  $data[5]['value'] = empty($_POST['comment_status']) ? 0 : 1 ;
  $data[6]['value'] = empty($_POST['comment_reviewed']) ? 0 : 1 ;
  function update_data ($id,$index,$message) {
    global $data;
    $rows = xiu_execute("UPDATE options SET value = '{$data[$index]['value']}' WHERE id = {$id};");
    if($rows < 0) {
      $GLOBALS['message'] = $message;
      $GLOBALS['succes'] = false;
    } else {
      $GLOBALS['message'] = '设置成功';
      $GLOBALS['succes'] = true;
    }
  }
  update_data(2,0,'设置logo失败');
  update_data(3,1,'设置站点名称失败');
  update_data(4,2,'设置站点描述失败');
  update_data(5,3,'设置站点关键词失败');
  update_data(7,5,'设置评论功能失败');
  update_data(8,6,'设置评论批准失败');
}
if($_SERVER['REQUEST_METHOD'] === 'POST') {
  update_settings();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Settings &laquo; Admin</title>
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
        <h1>网站设置</h1>
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
      <form class="form-horizontal" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
        <div class="form-group">
          <label for="site_logo" class="col-sm-2 control-label">网站图标</label>
          <div class="col-sm-6">
            <label class="form-image">
              <input id="logo" type="file" name="img_file" class="img_file">
              <img src="<?php echo $data[0]['value'];?>">
              <i class="mask fa fa-upload"></i>
              <input id="site_logo" name="site_logo" value="<?php echo $data[0]['value'];?>" type="hidden">
            </label>
          </div>
        </div>
        <div class="form-group">
          <label for="site_name" class="col-sm-2 control-label">站点名称</label>
          <div class="col-sm-6">
            <input id="site_name" value="<?php echo $data[1]['value'];?>" name="site_name" class="form-control" type="type" placeholder="站点名称">
          </div>
        </div>
        <div class="form-group">
          <label for="site_description" class="col-sm-2 control-label">站点描述</label>
          <div class="col-sm-6">
            <textarea id="site_description" name="site_description" class="form-control" placeholder="站点描述" cols="30" rows="6"><?php echo $data[2]['value'];?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label for="site_keywords" class="col-sm-2 control-label">站点关键词</label>
          <div class="col-sm-6">
            <input id="site_keywords" value="<?php echo $data[3]['value'];?>" name="site_keywords" class="form-control" type="type" placeholder="站点关键词">
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">评论</label>
          <div class="col-sm-6">
            <div class="checkbox">
              <label><input id="comment_status" name="comment_status" type="checkbox" <?php echo $data[5]['value'] == '1' ? 'checked' : ''; ?>>开启评论功能</label>
            </div>
            <div class="checkbox">
              <label><input id="comment_reviewed" name="comment_reviewed" type="checkbox" <?php echo $data[6]['value'] == '1' ? 'checked' : ''; ?>>评论必须经人工批准</label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-6">
            <button type="submit" class="btn btn-primary">保存设置</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php $current_page = 'settings' ?>
  <?php include 'inc/sidebar.php'; ?>

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
        $this.siblings('img').attr('src',imgSrc).siblings('[name="site_logo"]').val(imgSrc);
      }

    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
