<?php
/** ==========================================================
 * 【投稿】
 *
 * ● カスタム投稿用post_typeセット
 * ======================================================== */
/** **********************************************************
 * カスタム投稿用post_typeセット
 * ******************************************************** */
add_filter('template_include', 'custom_search_template');
function custom_search_template($template){
    if ( is_search() ){
        $post_types = get_query_var('post_type');
        foreach ( (array) $post_types as $post_type )
        $templates[] = "search-{$post_type}.php";
        $templates[] = 'search.php';
        $template = get_query_template('search',$templates);
    }
    return $template;
}

// add_action( 'new_to_publish', 'create_joboffer', 10, 3 );
// add_action( 'publish_to_publish', 'update_joboffer', 10, 3 );

function update_joboffer( $new_status, $old_status, $post ) {
    $datetime = date('Y-m-d H:i:s');
    file_put_contents(
        __DIR__.'/post.log',
        "【{$datetime}】\n{$new_status}\n{$old_status}\n{$post}\n",
        FILE_APPEND
    );
}
function create_joboffer( $new_status, $old_status, $post ) {
    $datetime = date('Y-m-d H:i:s');
    file_put_contents(
        __DIR__.'/post.log',
        "【{$datetime}】\n{$new_status}\n{$old_status}\n{$post}\n",
        FILE_APPEND
    );
}
