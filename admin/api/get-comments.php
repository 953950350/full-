<?php
require_once '../../functions.php';

xiu_get_current_user();

$count = (int)xiu_fetch_one ('
select 
	count(1) as count
	from comments inner join posts on comments.post_id = posts.id
	order by posts.created desc;
')['count'];

$page = empty($_GET['page']) ? 1 : intval($_GET['page']);
$page = $page < 1 ? 1 : $page;
$size = 20;
$max_page = (int)ceil($count/$size);
$page = $page > $max_page ? $max_page : $page ;
$offset = ($page - 1)*$size;

//获取最大页码


$data = xiu_fetch_all("
select 
	comments.*,
	posts.title as post_title
	from comments inner join posts on comments.post_id = posts.id
	order by comments.created desc
	limit {$offset},{$size};
	;");
	
$json = json_encode(array(
	'maxNum' => $max_page,
	'comments' => $data
));
header('Content-Type: application/json');

echo $json;