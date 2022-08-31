<?php
/**
* サイトのエントリーポイント
* 該当するテンプレート部分を読み込む
*
* @link https://github.com/zionboogie/smart-minimum
*
* @package WordPress
* @subpackage SMART-MINIMUM
* @since 1.0.0
*/

// ヘッダの出力
get_header();
?>

<!-- main -->
<main class="main-container">

<?php 

// シングルページ
if ( is_single() ) {
	get_template_part( 'template-parts/pg-single' );
// 固定ページ
} elseif ( is_page() ) {
	get_template_part( 'template-parts/pg-page' );
// カテゴリ
} elseif ( is_category() ) {
	get_template_part( 'template-parts/pg-category' );
// アーカイブ
} elseif ( is_archive() ) {
	get_template_part( 'template-parts/pg-archive' );
// トップページ
} elseif ( is_home() ) {
	get_template_part( 'template-parts/pg-home' );
// 検索ページ
} elseif ( is_search() ) {
	get_template_part( 'template-parts/pg-search' );
// 404ページ
} else {
	get_template_part( 'template-parts/pg-404' );
}

// サイドバーの出力
get_sidebar();

?>

</main>
<!-- /main -->

<?php
// フッタの出力
get_footer();
?>