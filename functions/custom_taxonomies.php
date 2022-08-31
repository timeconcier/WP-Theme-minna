<?php
function cptui_register_my_taxes() {
	/** **************************************************************************************************
	 * Taxonomy: ヘッダーカテゴリー.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "ヘッダーカテゴリー", "custom-post-type-ui" ),
		"singular_name" => __( "ヘッダーカテゴリー", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "ヘッダーカテゴリー", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'header_category', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_header",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
		"default_term"          => ['name' => 'other'],
	];
	register_taxonomy( "header_category", [ "post" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: 市町村.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "市町村", "custom-post-type-ui" ),
		"singular_name" => __( "市町村", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "市町村", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'cities', 'with_front' => true,  'hierarchical' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_cities",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "cities", [ "job_offers", "enterprises", "coupons", "events" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: 業種カテゴリー.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "業種カテゴリー", "custom-post-type-ui" ),
		"singular_name" => __( "業種カテゴリー", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "業種カテゴリー", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'cat_industry', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_industry",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "cat_industry", [ "post" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: 求人タグ.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "求人タグ", "custom-post-type-ui" ),
		"singular_name" => __( "求人タグ", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "求人タグ", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => false,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'tag_job_offers', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "tag_job_offers",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "tag_job_offers", [ "post" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: イベントカテゴリー.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "イベントカテゴリー", "custom-post-type-ui" ),
		"singular_name" => __( "イベントカテゴリー", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "イベントカテゴリー", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => false,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'cat_events', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_events",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "cat_events", [ "events" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: サービスカテゴリー.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "サービスカテゴリー", "custom-post-type-ui" ),
		"singular_name" => __( "サービスカテゴリー", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "サービスカテゴリー", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => false,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'cat_services', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_services",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "cat_services", [ "post" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: クーポンカテゴリー.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "クーポンカテゴリー", "custom-post-type-ui" ),
		"singular_name" => __( "クーポンカテゴリー", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "クーポンカテゴリー", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'cat_coupon', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_coupon",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "cat_coupon", [ "coupons" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: 事業所タグ. （廃止予定）
	 ************************************************************************************************** */
	// $labels = [
	// 	"name"          => __( "事業所タグ", "custom-post-type-ui" ),
	// 	"singular_name" => __( "事業所タグ", "custom-post-type-ui" ),
	// ];

	// $args = [
	// 	"label"                 => __( "事業所タグ", "custom-post-type-ui" ),
	// 	"labels"                => $labels,
	// 	"public"                => true,
	// 	"publicly_queryable"    => true,
	// 	"hierarchical"          => false,
	// 	"show_ui"               => true,
	// 	"show_in_menu"          => true,
	// 	"show_in_nav_menus"     => true,
	// 	"query_var"             => true,
	// 	"rewrite"               => [ 'slug' => 'tag_enterprises', 'with_front' => true, ],
	// 	"show_admin_column"     => false,
	// 	"show_in_rest"          => true,
	// 	"show_tagcloud"         => false,
	// 	"rest_base"             => "tag_enterprises",
	// 	"rest_controller_class" => "WP_REST_Terms_Controller",
	// 	"show_in_quick_edit"    => false,
	// 	"show_in_graphql"       => false,
	// ];
	// register_taxonomy( "tag_enterprises", [ "enterprises" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: 事業所ジャンル.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "事業所ジャンル", "custom-post-type-ui" ),
		"singular_name" => __( "事業所ジャンル", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "事業所ジャンル", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'cat_enterprise_genre', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_enterprise_genre",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "cat_enterprise_genre", [ "enterprises" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: 求人票ジャンル.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "求人票ジャンル", "custom-post-type-ui" ),
		"singular_name" => __( "求人票ジャンル", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "求人票ジャンル", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'cat_job_offer_genre', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_job_offer_genre",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "cat_job_offer_genre", [ "job_offers" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: 求人票ジャンル - 農業.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "求人票ジャンル - 農業", "custom-post-type-ui" ),
		"singular_name" => __( "求人票ジャンル - 農業", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "求人票ジャンル - 農業", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'cat_job_offer_genre_agriculture', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_job_offer_genre_agriculture",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "cat_job_offer_genre_agriculture", [ "job_offers" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: 求人票ジャンル - ウェディング.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "求人票ジャンル - ウェディング", "custom-post-type-ui" ),
		"singular_name" => __( "求人票ジャンル - ウェディング", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "求人票ジャンル - ウェディング", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'cat_job_offer_genre_wedding', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_job_offer_genre_wedding",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "cat_job_offer_genre_wedding", [ "job_offers" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: 求人票ジャンル - 宿泊.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "求人票ジャンル - 宿泊", "custom-post-type-ui" ),
		"singular_name" => __( "求人票ジャンル - 宿泊", "custom-post-type-ui" ),
	];

	$args = [
		"label"                 => __( "求人票ジャンル - 宿泊", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'cat_job_offer_genre_stay', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_job_offer_genre_stay",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "cat_job_offer_genre_stay", [ "job_offers" ], $args );


	/** **************************************************************************************************
	 * Taxonomy: 職種カテゴリー.
	 ************************************************************************************************** */
	$labels = [
		"name"          => __( "職種カテゴリー", "custom-post-type-ui" ),
		"singular_name" => __( "職種カテゴリー", "custom-post-type-ui" ),
	];


	$args = [
		"label"                 => __( "職種カテゴリー", "custom-post-type-ui" ),
		"labels"                => $labels,
		"public"                => true,
		"publicly_queryable"    => true,
		"hierarchical"          => true,
		"show_ui"               => true,
		"show_in_menu"          => true,
		"show_in_nav_menus"     => true,
		"query_var"             => true,
		"rewrite"               => [ 'slug' => 'cat_job_type', 'with_front' => true, ],
		"show_admin_column"     => false,
		"show_in_rest"          => true,
		"show_tagcloud"         => false,
		"rest_base"             => "cat_occupation",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
		"show_in_graphql"       => false,
	];
	register_taxonomy( "cat_job_type", [ "job_offers" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes' );
