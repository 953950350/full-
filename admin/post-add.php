<?php
require_once '../functions.php';

//判断用户是否登录
 $user = xiu_get_current_user();

//  获取分类数据
 $category = xiu_fetch_all('select * from categories;');

 function addpost() {
   global $category;
   global $user;
   $GLOBALS['success'] = false;
   if(empty($_POST['title'])) {
     $GLOBALS['message'] = '标题不能为空';
     return;
   }
   $GLOBALS['title'] = $_POST['title'];
   if(empty($_POST['content'])) {
    $GLOBALS['message'] = '文章内容不能为空';
    return;
  }
  $GLOBALS['content'] = $_POST['content'];
  if(empty($_POST['slug'])) {
    $GLOBALS['message'] = '请填写别名';
    return;
  }
  $GLOBALS['slug'] = $_POST['slug'];
  if(empty($_POST['feature'])) {
    $GLOBALS['message'] = '请上传特色图片';
    return;
  }
  $GLOBALS['feature'] = $_POST['feature'];
  if(empty($_POST['category'])) {
    $GLOBALS['message'] = '请选择分类';
    return;
  }
  $GLOBALS['new_category'] = $_POST['category'];
  if(empty($_POST['created'])) {
    $GLOBALS['message'] = '请选择时间';
    return;
  }
  $GLOBALS['created'] = $_POST['created'];
  if(empty($_POST['status'])) {
    $GLOBALS['message'] = '请选择状态';
    return;
  }
  $GLOBALS['title'] = $_POST['title'];
  $is_category = false;
  foreach ($category as $value) {
    if($value['id'] === $_POST['category']) {
      $is_category = true;
    }
  }
  if(!$is_category) {
    $GLOBALS['message'] = '请提交正确的状态';
    return;
  }
  $status_arr = ['drafted','published','trashed'];
  if(!in_array($_POST['status'],$status_arr)) {
    $GLOBALS['message'] = '请选择正确的状态';
    return;
  }
  $is_date = strtotime($_POST['created']) ? strtotime($_POST['created']) : false ;
  if(!$is_date) {
    $GLOBALS['message'] = '请日期格式错误';
    return;
  }
  $rows = xiu_execute("insert into posts values (null,'{$_POST['slug']}','{$_POST['title']}','{$_POST['feature']}','{$_POST['created']}','{$_POST['content']}',0,0,'{$_POST['status']}','{$user['id']}','{$_POST['category']}');");
  $GLOBALS['success'] = $rows > 0;
  $GLOBALS['message'] = $rows > 0 ? '保存成功' : '保存失败';

 }

 if($_SERVER['REQUEST_METHOD'] === 'POST') {
    addpost();
 }

?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Add new post &laquo; Admin</title>
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
        <h1>写文章</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <div class="alert alert-danger img-error" style="display:none">
        <strong>错误！</strong>
      </div>
      <?php if(isset($message)): ?>
      <?php if($success): ?>
      <div class="alert alert-success">
        <strong>正确！</strong><?php echo $message; ?>
      </div>
      <?php else: ?>
      <div class="alert alert-danger">
        <strong>错误！</strong><?php echo $message; ?>
      </div>
      <?php endif ?>
      <?php endif ?>
      <div class="alert alert-danger img-error" style="display:none">
        <strong>错误！</strong>
      </div>
      <form class="row" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="col-md-9">
          <div class="form-group">
            <label for="title">标题</label>
            <input id="title" class="form-control input-lg" value="<?php echo isset($GLOBALS['title']) ? $GLOBALS['title'] : '';?>" name="title" type="text" placeholder="文章标题">
          </div>
          <div class="form-group">
            <label for="content">文章内容</label>
            <script id="content" name="content" type="text/plain"><?php echo isset($GLOBALS['content']) ? $GLOBALS['content'] : '';?></script>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <label for="slug">别名</label>
            <input id="slug" class="form-control" value="<?php echo isset($GLOBALS['slug']) ? $GLOBALS['slug'] : '';?>" name="slug" type="text" placeholder="slug">
          </div>
          <div class="form-group">
            <label for="img_file">特色图像</label>
            <!-- show when image chose -->
            <img class="help-block thumbnail" style="display:none">
            <input id="img_file" class="form-control" name="img_file" type="file">
            <input name="feature" type="hidden" value="<?php echo isset($GLOBALS['img_file']) ? $GLOBALS['img_file'] : '';?>">
          </div>
          <div class="form-group">
            <label for="category">所属分类</label>
            <select id="category" class="form-control" name="category">
              <?php foreach ($category as $value): ?>
              <option value="<?php echo $value['id']?>"><?php echo $value['name']?></option>
              <?php endforeach ?>
            </select>
          </div>
          <div class="form-group">
            <label for="created">发布时间</label>
            <input id="created" class="form-control" name="created" type="datetime-local">
          </div>
          <div class="form-group">
            <label for="status">状态</label>
            <select id="status" class="form-control" name="status">
              <option value="drafted">草稿</option>
              <option value="published">已发布</option>
              <option value="trashed">回收站</option>
            </select>
          </div>
          <div class="form-group">
            <button class="btn btn-primary" type="submit">保存</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <?php $current_page = 'post-add' ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.config.js"></script>
  <script src="/static/assets/vendors/ueditor/ueditor.all.js"></script>
  <script>
    // 富文本编辑器的配置
    UE.getEditor('content',{
      initialFrameHeight: 800,
      autoHeight:false
    })
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
      $danger.fadeOut();
      var data = new FormData();
      data.append('avatar',file);
      var xhr = new XMLHttpRequest();
      xhr.open('POST','/admin/api/uploads.php');
      xhr.send(data);
      xhr.onload = function () {
        var imgSrc = this.responseText;
        $this.siblings('img').attr('src',imgSrc).show().siblings('[name="feature"]').val(imgSrc);
      }

    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
