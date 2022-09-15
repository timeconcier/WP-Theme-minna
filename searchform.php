<?php
/**
 *パーツ：検索フォーム
 */

if(is_single()){
	$post_type = get_post_type(get_the_ID());
} elseif(is_archive()) {
	$post_type = get_queried_object()->name;
	if (!$post_type) $post_type = $_GET['pt'];
} elseif(is_search()) {
	$post_type = $_GET['pt'];
} else {
	$post_type = 'post';
}
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<?php get_template_part("template-parts/search-filter", null, array('post_type' => $post_type)); ?>
</form>