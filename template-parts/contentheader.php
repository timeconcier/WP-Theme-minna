<?php
/**
* パーツ：記事一覧のループ部分
*/
?>

	<!-- entry-header -->
	<header id="post-<?php the_ID(); ?>" <?php post_class("entry-header"); ?>>
<?php
// 記事タイトルの出力
if ( is_singular() ){
	the_title( sprintf( '<h1 class="title"><a href="%s">', esc_url( get_permalink() ) ), '</a></h1>' );

} else {
	the_title( sprintf( '<h2 class="title"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' );
}
?>
		<div class="box-date">
<?php

$d1 = get_the_date();
$d2 = get_the_modified_date();
// 日付
printf( '登録日：<time class="entrydate publish" datetime="%1$s">%2$s</time>',
	get_the_date("Y-m-d"),
	$d1
);
if( $d1 != $d2 ){
	// 日付
	printf( '- 更新日：<time class="entrydate update" datetime="%1$s">%2$s</time>',
		get_the_modified_date("Y-m-d"),
		$d2
	);
}

?>

		</div>
		<div class="postmeta">
			カテゴリ：<span class="category-list"><?php
			// カテゴリの出力
			smart_entry_category();
			?></span><br>
			タグ：<span class="tag"><?php
			// タグの出力
			smart_entry_tag();
			?></span>
		</div>
	</header>
	<!-- /entry-header -->
