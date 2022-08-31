<?php
/**
 *パーツ：検索フォーム
 */

if(is_single()){
	$postType = get_post_type(get_the_ID());
} elseif(is_archive()) {
	$postType = get_queried_object()->name;
} elseif(is_search()) {
	$postType = $_GET['pt'];
} else {
	$postType = null;
}

$postTypes = [
	'enterprises' => [
		'name' => '事業所',
		'color' => 'success',
	],
	'job_offers'  => [
		'name' => '求人',
		'color' => 'info',
	],
	'events'      => [
		'name' => 'イベント',
		'color' => 'warning',
	],
	'coupons'     => [
		'name' => 'クーポン',
		'color' => 'danger',
	],
];
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="form-group px-2">
		<div class="form-group">
			<h2 class="text-center d-block d-sm-none text-outline">みんなのまちサイト</h2>
			<div class="input-group mb-3">

				<?php if(!$postType): ?>
					<input type="text" class="form-control" placeholder="例：◯◯市 カフェ お店" value="<?php echo get_search_query(); ?>" name="s" title="検索" />

					<select class="form-select" name="pt" style="max-width:150px">
						<?php foreach($postTypes as $pt => $ptObj): ?>
						<option value="<?= $pt; ?>" <?= ($postType == $pt) ? 'selected' : ''; ?>><?= $ptObj['name']; ?></option>
						<?php endforeach; ?>
					</select>

					<button class="btn btn-primary px-4" type="submit" id="button-addon2">
						<i class="fas fa-search"></i>
					</button>
				<?php endif; ?>

			</div>
		</div>
	</div>
	<?php if ($postType): ?>
		<?php get_template_part("template-parts/search-filter", null, array('post_type' => $postType)); ?>
	<?php endif; ?>


</form>
