<?php

$childThemeDir = get_stylesheet_directory();
include_once('/home/tc2/timeconcier.jp/public_html/forline/tccom/tcLibKintone.php');

include_once("{$childThemeDir}/functions/constants.php");
include_once("{$childThemeDir}/functions/admin_actions.php");
include_once("{$childThemeDir}/functions/wp_cron.php");
include_once("{$childThemeDir}/functions/custom_post_endpoints.php");
include_once("{$childThemeDir}/functions/custom_post_types.php");
include_once("{$childThemeDir}/functions/custom_taxonomies.php");
include_once("{$childThemeDir}/functions/category_actions.php");
include_once("{$childThemeDir}/functions/front_actions.php");
include_once("{$childThemeDir}/functions/post_actions.php");
include_once("{$childThemeDir}/functions/wp_ajax.php");
include_once("{$childThemeDir}/functions/other.php");

add_filter( 'posts_request', function ( $input ) {
	// echo '<!--'.PHP_EOL;
	// print_r($input);
	// echo '-->'.PHP_EOL;
	return $input;
} );


/* ========================================================
WP Rest API関連
=========================================================*/
/** **********************************************************
 * 最大取得件数
 * ******************************************************** */
add_filter('rest_post_collection_params', 'get_rest_max_posts', 10, 2);
function get_rest_max_posts($params, WP_Post_Type $post_type)
{
	if ('post' === $post_type->name && isset($params['per_page'])) {
		$params['per_page']['maximum'] = 200;
	}

	return $params;
}

/** **********************************************************
 * ACF to WP Rest APIを許可
 * ******************************************************** */
if (is_user_logged_in()) {
	// Enable the option show in rest
	add_filter('acf/rest_api/field_settings/show_in_rest', '__return_true');

	// Enable the option edit in rest
	add_filter('acf/rest_api/field_settings/edit_in_rest', '__return_true');

	add_theme_support('post-thumbnails', array('enterpprises', 'job_offers'));

	// function disable_rest_api() {
	//     return new WP_Error( 'disabled', __( 'REST API is disabled.' ), array( 'status' => rest_authorization_required_code() ) );
	// }
	// add_filter( 'rest_authentication_errors', 'disable_rest_api' );
}


/** **********************************************************
 * ACF Pro アップデート通知回避
 * ******************************************************** */
function filter_hide_update_notice($data) {
	if (isset($data->response['advanced-custom-fields-pro/acf.php'])) {
		unset($data->response['advanced-custom-fields-pro/acf.php']);
	}
	return $data;
}
add_filter('site_option__site_transient_update_plugins', 'filter_hide_update_notice');


/** **********************************************************
 * カテゴリーの最下層のタームを出力
 *
 * @param int $post_id
 * @param string $tax_name
 * @return void
 ********************************************************** */
function get_term_descendants ( $post_id = '', $tax_name = 'category' ) {
	$terms = get_the_terms( $post_id, $tax_name );
	if ( empty( $terms )) return false;
	$candidate = $terms;
	$count = count( $terms );
	if ( $count > 1 ){
		foreach( $terms as $key => $term ){
			foreach( $terms as $term2 ){
				if ( term_is_ancestor_of( $term->term_id, $term2->term_id, $tax_name ) ) {
					unset( $candidate[$key] );
					break;
				}
			}
		}
	}
	return $candidate;
}


/** **********************************************************
 * カテゴリーの2階層目以降のタームを出力
 *
 * @param int $post_id
 * @param string $tax_name
 * @return void
 ********************************************************** */
function get_term_without_parent ( $post_id = '', $tax_name = 'category' ) {
	$terms = get_the_terms( $post_id, $tax_name );
	if ( empty( $terms )) return [];
	$candidate = [];
	foreach($terms as $term) {
		if($term->parent > 0) $candidate[] = $term;
	}
	return $candidate;
}

add_filter( 'post_thumbnail_html', 'custom_attribute' );
function custom_attribute( $html ){
    // width height を削除する
    $html = preg_replace('/(width|height)="\d*"\s/', '', $html);
    return $html;
}