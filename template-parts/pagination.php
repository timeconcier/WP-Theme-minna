<?php
/**
* パーツ：ページネーション
*/

global $wp_rewrite, $_GET;

$paginate_base = get_pagenum_link(1);
if ( strpos($paginate_base, '?') || ! $wp_rewrite->using_permalinks() ) {
	$paginate_format	= '';
	$paginate_base		= add_query_arg('paged', '%#%');
} else {
	$paginate_format = (substr($paginate_base, -1 ,1) == '/' ? '' : '/')
		. user_trailingslashit('page/%#%/', 'paged');
	$paginate_base .= '%_%';
}
$pg_navi	= paginate_links( array(
	'base'		=> $paginate_base,
	'format'	=> $paginate_format,
	'total'		=> $wp_query->max_num_pages,
	'mid_size'	=> 7,
	'current'	=> ($paged ? $paged : 1),
));
?>

<div class="pagination-nav"><?= $pg_navi; ?></div>