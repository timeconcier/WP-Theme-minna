<?php
function cptui_register_my_taxes() {
	$taxonomies = json_decode(file_get_contents(__DIR__.'/custom_taxonomies.json'));

	/** **************************************************************************************************
	 * タクソノミー生成
	 ************************************************************************************************** */
	foreach($taxonomies as $i => $tx) {

		$args = [
			"label"                 => __( $tx->label, "custom-post-type-ui" ),
			"labels"                =>  [
				"name"          => __( $tx->label, "custom-post-type-ui" ),
				"singular_name" => __( $tx->label, "custom-post-type-ui" ),
			],
			"public"                => true,
			"publicly_queryable"    => true,
			"hierarchical"          => $tx->hierarchical,
			"show_ui"               => true,
			"show_in_menu"          => true,
			"show_in_nav_menus"     => true,
			"query_var"             => true,
			"rewrite"               => [ 'slug' => $tx->name, 'with_front' => true, ],
			"show_admin_column"     => false,
			"show_in_rest"          => true,
			"show_tagcloud"         => false,
			"rest_base"             => $tx->name,
			"rest_controller_class" => "WP_REST_Terms_Controller",
			"show_in_quick_edit"    => false,
			"show_in_graphql"       => false,
			// "default_term"          => ['name' => 'other'],
		];
		register_taxonomy( $tx->name, $tx->post_type, $args );
	}
}
add_action( 'init', 'cptui_register_my_taxes' );
