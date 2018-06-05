<?php
  require_once '../functions.php';

//判断用户是否登录
  xiu_get_current_user();
  $page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
  $size = 20;

  $where = '1 = 1';
  $search = '';

//判断是否选择分类
 if(isset($_GET['classify']) && $_GET['classify'] !== 'all' ) {
   $where .= ' and posts.category_id= '.$_GET['classify'];
   $search .= '&classify='.$_GET['classify'];
 }
 //判断是否选择状态
 if(isset($_GET['state']) && $_GET['state'] !== 'all' ) {
  $where .= ' and posts.status= '." '{$_GET['state']}' ";
  $search .= '&state='.$_GET['state'];
  }


  //获取总数据的条数
$total_count = (int)xiu_fetch_one ("select 
count(1) as count
from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id 
where {$where}
")['count'];

//获取总数据的页数
$total_pages = (int)ceil($total_count/$size);

//判断传过来的page是否合理,如果不合理，修改page
$page = $page > 1 ? $page : 1 ;
$page = $page < $total_pages ? $page : $total_pages ;
$offset = ($page - 1) * $size;



 //获取数据
  $data_posts = xiu_fetch_all ("select 
	posts.id,
	posts.title,
	users.nickname as user_name,
	categories.name as category_name,
	posts.created,
	posts.status
from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id 
where {$where}
order by posts.created desc
limit {$offset} , {$size}");

//获取分类的数据
$data_classify = xiu_fetch_all ("select * from categories;");

//页码的逻辑

  $visiables = 5 ;
  //计算最大和最小展示的页码
  $begin = $page - ($visiables - 1)/2 ;
  $end = $begin + $visiables;

  //重点考虑页码的合理性问题
  $begin = $begin < 1 ? 1 : $begin;//确保begin不会小于1
  $end = $begin + $visiables;//同步两者的关系，end始终比begin大5
  $end = $end > $total_pages ? $total_pages+1 : $end;//确保end不会大于total_pages
  $begin = $end - $visiables;//因为到了51行会改变end，那么就可能打破begin和end的关系
  $begin = $begin < 1 ? 1 : $begin;//确保不能小于1

  /*
  1. 当前页码显示高亮
  2. 左侧和右侧各有2个页码
  3. 开始页码不能小于1
  4. 结束页码不能大于最大页数
  5. 当前页码不为1时显示上一页
  6. 当前页码不为最大值是显示下一页
  7. 当开始页码不等于1时显示省略号
  8. 当结束页码不等于最大时显示省略号
  */
  function convert_status ($status) {
    $array_status = array(
      'published' => '已发布',
      'drafted' => '草稿',
      'trashed' => '回收站'
    );
    return isset($array_status[$status]) ? $array_status[$status] : '未知';
  }
  function convert_date ($created) {
    $timestamp = strtotime($created);
    return date('Y年m月d日<b\r>H:i:s',$timestamp); 
  }



  // 这种方法获取名字和分类对造成多次查询数据库的问题，应该使用数据库关联查询的方法
  // function convert_author($user_id) {
  //   return xiu_fetch_one("select nickname from users where {'$user_id'}");
  // }
  // function convert_category($category_id) {
  //   return xiu_fetch_one("select name from categories where {'$category_id'}");
  // }




?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
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
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF'];?>" method="get">
          <select name="classify" class="form-control input-sm">
            <option value="all">所有分类</option>
            <?php foreach($data_classify as $value): ?>
            <option value="<?php echo $value['id']; ?>" <?php echo  (isset($_GET['classify']) && $_GET['classify'] === $value['id']) ? 'selected':''; ?>><?php echo $value['name']; ?></option>
            <?php endforeach ?>
          </select>
          <select name="state" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="published"<?php echo (isset($_GET['state']) && $_GET['state'] === 'published') ? ' selected':'';  ?>>已发布</option>
            <option value="drafted"<?php echo (isset($_GET['state']) && $_GET['state'] === 'drafted') ? ' selected':'';  ?>>草稿</option>
            <option value="trashed"<?php echo (isset($_GET['state']) && $_GET['state'] === 'trashed') ? ' selected':'';  ?>>回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <?php if($page > 1): ?>
          <li><a href="?page=<?php echo $page - 1; ?><?php echo $search; ?>">上一页</a></li>
          <?php endif ?>
          <?php if($page > $visiables): ?>
          <li><a href="?page=<?php echo $page - $visiables; ?><?php echo $search; ?>">...</a></li>
          <?php endif ?>
          <?php for($i = $begin ; $i < $end ; $i++ ): ?>
          <li<?php echo $page === $i ? ' class="active" ':''; ?>><a href="?page=<?php echo $i ?><?php echo $search; ?>"><?php echo $i ?></a></li>
          <?php endfor ?>
          <?php if($page < $total_pages): ?>
          <li><a href="?page=<?php echo $page + $visiables; ?><?php echo $search; ?>">...</a></li>
          <?php endif ?>
          <?php if($page < $total_pages): ?>
          <li><a href="?page=<?php echo $page + 1; ?><?php echo $search; ?>">下一页</a></li>
          <?php endif ?>
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
        <?php if($data_posts):  ?>
        <?php foreach ($data_posts as $value): ?>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $value['title']; ?></td>
            <td><?php echo $value['user_name']; ?></td>
            <td><?php echo $value['category_name']; ?></td>
            <td class="text-center"><?php echo convert_date($value['created']); ?></td>
            <td class="text-center"><?php echo convert_status($value['status']); ?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="/admin/delete-posts.php?id=<?php echo $value['id']; ?>" class="btn btn-danger btn-xs">删除</a>
            </td>
          </tr>
          <?php endforeach ?>
          <?php endif ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'posts'; ?>
  <?php include 'inc/sidebar.php'; ?>
  
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>
