<?php
function cptui_register_my_cpts() {
	$post_types = json_decode(file_get_contents(__DIR__.'/custom_post_types.json'));

	/** **************************************************************************************************
	 * 投稿タイプ生成
	 ************************************************************************************************** */
	foreach($post_types as $i => $pt) {
		$labels = [
			'name'           => $pt->label,
			'singular_name'  => $pt->label,
		];

		$args = [
			'label'                 => $pt->label,
			'labels'                => $labels,
			'description'           => '',
			'menu_icon'				=> $pt->icon,
			'public'                => true,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'show_in_rest'          => true,
			'rest_base'             => $pt->name,
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'has_archive'           => $pt->name,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'delete_with_user'      => false,
			'exclude_from_search'   => false,
			'capability_type'       => 'post',
			'map_meta_cap'          => true,
			'hierarchical'          => false,
			'rewrite'               => [
				'slug'       => $pt->name,
				'with_front' => true
			],
			'query_var'             => true,
			'supports'              => $pt->supports,
			'show_in_graphql'       => false,
		];

        // 生成
		register_post_type( $pt->name, $args );
	}


	/** ***************************************************
	 * Post Type: ピックアップ.
	 *************************************************** */
	$labels = [
		"name" 			=> 'ピックアップ',
		"singular_name" => 'ピックアップ',
	];

	$args = [
		"label" 				=> 'ピックアップ',
		"labels" 				=> $labels,
		"description" 			=> "",
		'menu_icon'				=> 'dashicons-images-alt2',
		"public" 				=> true,
		"publicly_queryable" 	=> true,
		"show_ui" 				=> true,
		"show_in_rest" 			=> false,
		"rest_base" 			=> "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" 			=> false,
		"show_in_menu" 			=> true,
		"show_in_nav_menus" 	=> false,
		"delete_with_user" 		=> false,
		"exclude_from_search" 	=> true,
		"capability_type"	 	=> "post",
		"map_meta_cap" 			=> true,
		"hierarchical" 			=> false,
		"can_export" 			=> false,
		"rewrite" 				=> [
			"slug" => "pickups",
			"with_front" => true
		],
		"query_var" 			=> true,
		"supports" 				=> [
			'title',
			// 'editor',
			// 'thumbnail',
			// 'excerpt',
			// 'trackbacks',
			'custom-fields',
			// 'comments',
			// 'revisions',
			'author',
			// 'page-attributes',
			// 'post-formats'
		],
		"show_in_graphql" 		=> false,
	];
	register_post_type( "pickups", $args );
}
add_action( 'init', 'cptui_register_my_cpts' );