<?php
require_once '../functions.php';

//判断用户是否登录
 xiu_get_current_user();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
  <style>
    
    .ojlge-box .loading {
      box-sizing: border-box;
      position: relative;
      display: inline-block;
      padding: 0.5em;
      vertical-align: middle;
      text-align: center;
      background-color: transparent;
      border: 5px solid transparent;
      border-top-color: #f60;
      border-bottom-color: #f60;
      border-radius: 50% 0;
      box-shadow: 0 0 0.25em #f60 inset;
    }
    #loading {
      position: fixed;
      display: flex;
      top: 0;
      bottom: 0;
      right: 0;
      left: 0;
      align-items: center;
      justify-content: center;
      background-color: rgba(0,0,0,0.4);
      z-index: 999;
    }

    .ojlge-box .outer {
      animation: ojlge-spin 1.25s infinite linear;
    }

    .ojlge-box .inner {
      animation: ojlge-spin 1.35s infinite linear;
    }

    @keyframes ojlge-spin {
      0% {
        transform: rotateZ(0deg);
      }
      100% {
        transform: rotateZ(360deg);
      }
    }
      
  </style>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">
  <?php include 'inc/navbar.php'; ?>
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right">
        </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th>评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="150">操作</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page = 'comments'; ?>
  <?php include 'inc/sidebar.php'; ?>

    
  <div id="loading" style="display: none">
    <div class="ojlge-box">
      <div class="loading outer">
        <div class="loading inner"></div>
      </div>
    </div>
  </div>
  
  <script type="text/x-jsrender" id="j-specCard">
    <tr class="{{if status == 'held'}} warning{{else status == 'rejected'}}danger{{/if}}" data-id="{{:id}}">
      <td class="text-center"><input type="checkbox"></td>
      <td>{{:author}}</td>
      <td>{{:content}}</td>
      <td>{{:post_title}}</td>
      <td>{{:created}}</td>
      <td>{{if status == 'held'}}待审核{{else status == 'rejected'}}草稿{{else}}发表{{/if}}</td>
      <td class="text-center">
      {{if status == 'rejected' || status == 'approved' || status == 'trashed'}}
        <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
      {{else status == 'held'}}
      <a href="javascript:;" class="btn btn-info btn-xs">批准</a>
      <a href="javascript:;" class="btn btn-warning btn-xs">拒绝</a>
      <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
      {{/if}}
      </td>
    </tr>
  </script>
  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script>
    $(function ($) {
      //发送ajax获取数据时的加载动画
        $(document)
      .ajaxStart(function () {
        NProgress.start();
        $('#loading').fadeIn();
        
      })
      .ajaxStop(function () {
        NProgress.done();
        $('#loading').fadeOut();
      })

      var currentPage = parseInt(window.sessionStorage.getItem('last_comments_page')) || 1;

      //发送ajax请求获取数据的函数
      function getComments(page) {
          $.getJSON('/admin/api/get-comments.php',{page : page},function (res) {
          //初始化模板引擎
          var jsrender = $.templates('#j-specCard');
          //将数据添加到模板引擎中
          var html = jsrender(res['comments']);
          //接受从服务器传递过来的页面最大值
          var maxPage = parseInt(res['maxNum']);
          //添加到页面中
          $('tbody').html(html);
          if(page > maxPage) {
            getComments(maxPage);
            return;
          }
          //分页组件
          $('.pagination').twbsPagination('destroy');
          currentPage = page;
          changePage(maxPage);
        })
      }
      
      //分页组件函数
      function changePage(maxPage) {
          $('.pagination').twbsPagination({
          first: '&laquo',
          last: '&raquo',
          prev: '上一頁',
          next: '下一頁',
          totalPages: maxPage,
          visablepages: 5,
          startPage:currentPage,
          initiateStartPageClick: false,
          onPageClick: function (e,page) {
            getComments(page);
            //将当前的页数储存到浏览器中，防止刷新之后回到第一页
            window.sessionStorage.setItem('last_comments_page',page);
          }
        })
      }

      getComments(currentPage);

      //删除的逻辑
      var $tbody = $('tbody');
      $tbody.on('click',function (e) {
          var $delete = $(e.target);
          if($delete.html() !== '删除') return;
          var $id = $delete.parent().parent().data('id');
         
      $.get('/admin/api/delete-comments.php',{id : $id},function (res) {
          if(res !== '1') return;
          getComments(currentPage);
           
        })
      })

      // 修改状态逻辑
      $tbody.on('click','.btn-info,.btn-warning',function () {
        var $id = $(this).parent().parent().data('id');
        $operate = $(this).html() == '批准'? 1 : 0 ;
        $.get('/admin/api/change-comments.php',{id : $id,operate : $operate },function (res) {
          if(res !== '1') return;
          getComments(currentPage);
        })
      })
      
      var changeArr = [],
          $btnBatch = $('.btn-batch');
      // 批量操作
      $tbody.on('click','input:checkbox',function () {
        var id = $(this).parent().parent().data('id');
        if($(this).prop('checked')) {
          changeArr.push(id);
        } else {
          changeArr.splice(changeArr.indexOf(id),1);
        }
        if(changeArr.length) {
          
          $btnBatch.fadeIn(); 
          //批量修改状态
        }else {
          $btnBatch.fadeOut();
        }
      })
      
      //批量删除
      $btnBatch.children('.btn-danger').on('click',function () {
        $.get('/admin/api/delete-comments.php',{id : changeArr.toString()},function (res) {
          if(!res) return;
          changeArr = [];
          getComments(currentPage);
          if(changeArr.length) {
          
          $btnBatch.fadeIn(); 
          //批量修改状态
        }else {
          $btnBatch.fadeOut();
        }
        })
      })
      $btnBatch.children('.btn-info,.btn-warning').on('click',function () {
        $operate = $(this).html() == '批量批准'? 1 : 0 ;
        $.get('/admin/api/change-comments.php',{id : changeArr.toString(),operate : $operate},function (res) {
          if(!res) return;
          changeArr = [];
          getComments(currentPage);
          if(changeArr.length) {
          
          $btnBatch.fadeIn(); 
          //批量修改状态
          }else {
            $btnBatch.fadeOut();
          }
        })
      })


    })
  </script>
  <script>NProgress.done()</script>
</body>
</html>
