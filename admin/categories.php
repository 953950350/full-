<?php

  require_once '../functions.php';

  //判断用户是否登录
  xiu_get_current_user();

  //添加的函数
  function add_name () {
    if (empty($_POST['name'])) {
      $GLOBALS['message'] = '请填写分类名称';
      $GLOBALS['success'] = false;
      return;
    }
    if (empty($_POST['slug'])) {
      $GLOBALS['message'] = '请填写别名';
      $GLOBALS['success'] = false;
      return;
    }
    $sql = "insert into categories values (null,'{$_POST['slug']}','{$_POST['name']}');";
    $rows = xiu_execute ($sql);
    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['message'] = $rows <=0 ? '添加失败' :'添加成功';
  }

  //编辑的函数
  function change_name() {
    global $menu_data;
    $id = $_GET['id'];
    $slug = empty($_POST['slug']) ? $menu_data['slug'] : $_POST['slug'];
    $menu_data['slug'] = $slug;
    $name = empty($_POST['name']) ? $menu_data['name'] : $_POST['name'];
    $menu_data['name'] = $name;
    $sql = "update categories set slug = '{$slug}' , name = '{$name}' where id = {$id};";
    $rows = xiu_execute ($sql);
    $GLOBALS['success'] = $rows > 0;
    $GLOBALS['message'] = $rows <=0 ? '编辑失败' :'编辑成功';
  }

  // 判断时添加还是编辑
  if(isset($_GET['id'])) {
    $menu_data = xiu_fetch_one("select * from categories where id = {$_GET['id']}");
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      change_name();
    }
  } else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      add_name();
    }
  }


  $data = xiu_fetch_all('select * from categories');




?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Categories &laquo; Admin</title>
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
        <h1>分类目录</h1>
      </div>
      <!-- 有错误信息时展示 -->
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
      <div class="row">
        <div class="col-md-4">
         <?php if(isset($menu_data)): ?>
          <form action="<?php echo $_SERVER['PHP_SELF'].'?id='.$_GET['id']; ?>" method="post">
            <h2>编辑《<?php echo $menu_data['name']; ?>》</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称" value="<?php echo $menu_data['name']; ?>">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug" value="<?php echo $menu_data['slug']; ?>">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
            </div>
            <div class="form-group">
              <button class="btn btn-primary" type="submit">编辑</button>
            </div>
          </form>
          <?php else:?>
          <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
            <h2>添加新分类目录</h2>
            <div class="form-group">
              <label for="name">名称</label>
              <input id="name" class="form-control" name="name" type="text" placeholder="分类名称">
            </div>
            <div class="form-group">
              <label for="slug">别名</label>
              <input id="slug" class="form-control" name="slug" type="text" placeholder="slug">
              <p class="help-block">https://zce.me/category/<strong>slug</strong></p>
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
            <a class="btn btn-danger btn-sm" id="btn_delete" href="deleteCategories.php" style="visibility:hidden">批量删除</a>
          </div>
          <table class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th class="text-center" width="40"><input type="checkbox" class="allCheck"></th>
                <th>名称</th>
                <th>Slug</th>
                <th class="text-center" width="100">操作</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($data as $value): ?>
              <tr>
                <td class="text-center"><input class="checkbox-items" type="checkbox" data-id = "<?php echo $value['id']; ?>"></td>
                <td><?php echo $value['name']; ?></td>
                <td><?php echo $value['slug']; ?></td>
                <td class="text-center">
                  <a href="/admin/categories.php?id=<?php echo $value['id']; ?>" class="btn btn-info btn-xs">编辑</a>
                  <a href="/admin/deleteCategories.php?id=<?php echo $value['id']; ?>" class="btn btn-danger btn-xs">删除</a>
                </td>
              </tr>
              <?php endforeach ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <?php $current_page = 'categories' ?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
  <script>

  $(function ($) {
    var $itemsCheck = $('.checkbox-items');
    var $btnDelete = $('#btn_delete');
    var checkArr = [];
    $itemsCheck.on('change',function () {
      var id = $(this).data('id');
      if($(this).prop('checked')) {
        //includes()ES5新增的数组方法，检查数组是否有指定元素
        checkArr.includes(id) || checkArr.push(id);
      }else {
        checkArr.splice(checkArr.indexOf(id),1);
      }
      checkArr.length ? $btnDelete.css('visibility','visible'):$btnDelete.css('visibility','hidden');
      $btnDelete.prop('search','?id='+ checkArr);
    })

    $('thead input').on('change',function () {
      $itemsCheck.prop('checked',$(this).prop('checked')).trigger('change');
    })

  })
    // ## version 1 =================================
    // $(function ($) {
    //   var itemsCheck = $('.checkbox-items');
    //   $('.checkbox-items').on('click',function () {
    //     var checkArr = [];
    //     itemsCheck.each(function () {
    //       if($(this).prop('checked')) {
    //         checkArr.push($(this).attr('index'));
    //       }
    //     })
    //     if (checkArr.length>1) {
    //       $('.btn-sm').css('visibility','visible').attr('href','deleteCategories.php?id='+checkArr.join(','));
    //     }else {
    //       $('.btn-sm').css('visibility','hidden');
    //     }

    //   })
    // })
  </script>
</body>
</html>
