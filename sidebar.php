<?php
/*
パーツ：サイドバー
*/
?>
<div class="sidebar-container">
	<!-- widget-category -->
	<aside class="widget-container -category">
		<h2 class="title">カテゴリ</h2>
		<ul>
<?php wp_list_categories('title_li=&hide_empty=0&orderby=count&order=asc') ?>
		</ul>
	</aside>
	<!-- /widget-category -->

	<!-- widget-tagcloud -->
	<aside class="widget-container -tagcloud">
		<h2 class="title">タグクラウド</h2>
		<div class="tagcloud-list">
<?php wp_tag_cloud('smallest=10&largest=10&orderby=count&order=DESC'); ?>
		</div>
	</aside>
	<!-- /widget-tagcloud -->

	<!-- widget-archives -->
	<aside class="widget-container -archives">
		<h2 class="title">アーカイブ</h2>
		<ul>
<?php wp_get_archives( 'type=monthly' ); ?>
		</ul>
	</aside>
	<!-- /widget-archives -->
</div>

<div class="sidebar-container">
<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
	<!-- widget-dynamic -->
	<aside class="widget-container -dynamic">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</aside>
	<!-- /widget-dynamic -->
<?php endif; ?>
</div>
