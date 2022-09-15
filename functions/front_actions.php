<?php
/** ==========================================================
 * 【フロント】
 * ● 半角文字を含む検索をした際の404エラーを回避
 * ● 投稿タイプを取得
 * ● メニューの種類と位置を定義
 * ● メニューの種類と位置を定義
 * ● メニューの<li>からID除去
 * ● wp_nav_menuのliにclass追加
 * ● wp_nav_menuのaにclass追加
 * ● CSS、JSを追加
 * ● 表示デバイス判定
 * ● WP_Queryでカスタムフィールドとタイトルを条件にOR検索する
 * ● 郵便番号に自動でハイフン追加
 * ======================================================== */

/** **********************************************************
 * 半角文字を含む検索をした際の404エラーを回避
 * ******************************************************** */
add_filter( 'template_redirect',  function() {
	global $wp_query;

	if ( is_404() && ( isset( $wp_query->query['paged'] ) && $wp_query->query['paged'] >= 2 ) ) {
		status_header( 200 );
        $wp_query->is_404 = false;
        $wp_query->is_archive = false;
        $wp_query->is_search = true;
	}
});

/** **********************************************************
 * 投稿タイプを取得
 * ******************************************************** */
function getPostType(){
    if(is_single()){
        return get_post_type(get_the_ID());
    } elseif(is_archive()) {
        return get_queried_object()->name;
    } elseif(is_search()) {
        return $_GET['pt'];
    } else {
        return null;
    }
}


/** **********************************************************
 * メニューの種類と位置を定義
 * ******************************************************** */
// This theme uses wp_nav_menu() in two location.
add_action( 'after_setup_theme', 'register_my_menus' );
function register_my_menus() {
    register_nav_menus( array(
        'primary' => __( 'Navigation Bar', 'first' ),
        'header'  => __( 'Header Menu', 'first' ),
        'footer'  => __( 'Footer Menu', 'first' ),
    ) );
}

/** **********************************************************
 * メニューの<li>からID除去
 * ******************************************************** */
add_filter('nav_menu_item_id', 'removeMenuId', 10);
function removeMenuId( $id ){
    return $id = array();
}

/** **********************************************************
 * wp_nav_menuのliにclass追加
 * ******************************************************** */
// add_filter('nav_menu_css_class', 'add_additional_class_on_li', 1, 3);
// function add_additional_class_on_li($classes, $item, $args) {
//     // $classes = array();
//     if (isset($args->li_class)) {
//         $classes[] = $args->li_class;
//     }
//     return $classes;
// }

/** **********************************************************
 * wp_nav_menuのaにclass追加
 * ******************************************************** */
add_filter('nav_menu_link_attributes', 'add_additional_class_on_a', 1, 3);
function add_additional_class_on_a($classes, $item, $args) {
    if (isset($args->a_class)) {
        $classes['class'] = $args->a_class;
    }
    return $classes;
}


/** **********************************************************
 * CSS、JSを追加
 * ******************************************************** */
add_action( 'wp_enqueue_scripts', function() {
    $page = get_post( get_the_ID() );
    $slug = (!empty($page) && !is_search()) ? $page->post_name : '';

    $postType = get_post_type( get_the_ID() );

    $childTheme_dir = get_stylesheet_directory_uri();
    $childTheme_path = get_stylesheet_directory();

    if(!is_admin()){ //管理画面以外
        wp_enqueue_script('jquery');
        remove_action('wp_head', 'wp_print_scripts');
        remove_action('wp_head', 'wp_print_head_scripts', 9);
        remove_action('wp_head', 'wp_enqueue_scripts', 1);
        // add_action('wp_footer', 'wp_print_scripts');
        // add_action('wp_footer', 'wp_print_head_scripts');
        // add_action('wp_footer', 'wp_enqueue_scripts');
    }

    /** ************************************************************
     * StyleSheets - CSS
     ************************************************************ */
    // Google Fonts
	wp_enqueue_style( 'g-font-kosugi', 'https://fonts.googleapis.com/css2?family=Kosugi+Maru&display=swap', array(), null );
	wp_enqueue_style( 'g-font-potta', 'https://fonts.googleapis.com/css2?family=Potta+One&display=swap', array(), null );
    // Swiper
    wp_enqueue_style( 'swiper', 'https://unpkg.com/swiper@7/swiper-bundle.min.css', array(), '7' );
    // Font Awesome
    wp_enqueue_style('fontawesome', 'https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5/css/all.min.css', array(), '1');
    // ユーザーページで動作する
    if(file_exists("{$childTheme_path}/vue-member/v-{$slug}.js")){
        wp_enqueue_style( 'mdi', 'https://cdn.jsdelivr.net/npm/@mdi/font@6/css/materialdesignicons.min.css', array(), '6' );
        wp_enqueue_style( 'vuetify', 'https://cdn.jsdelivr.net/npm/vuetify@2/dist/vuetify.min.css', array(), '2' );
        wp_enqueue_style( 'v-gtable', 'https://cdn.jsdelivr.net/npm/vue-good-table@2/dist/vue-good-table.css', array(), '2' );
        wp_enqueue_style( 'v-sel', 'https://cdn.jsdelivr.net/npm/vue-select@3/dist/vue-select.min.css', array(), '3' );
        wp_enqueue_style( 'main', "{$childTheme_dir}/_src/css/v-style.css", array(), date('YmdHis') );
    } else {
        // Bootstrap5
        wp_enqueue_style( 'bs', 'https://cdn.jsdelivr.net/npm/bootswatch@5/dist/lux/bootstrap.min.css', array(), '5' );
        wp_enqueue_style( 'bs-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8/font/bootstrap-icons.min.css', array(), '1' );
        wp_enqueue_style( 'bs-comp', "{$childTheme_dir}/_src/css/bs-complement.css", array(), date('YmdHis') );
        // main css
        wp_enqueue_style( 'main', "{$childTheme_dir}/_src/css/style.css", array(), date('YmdHis') );
    }




    /** ************************************************************
     * JavaScripts - JS
     ************************************************************ */
    wp_enqueue_script( 'fn', "{$childTheme_dir}/_src/js/functions.js", array(), date('YmdHis'), true );

    // jQuery
    wp_enqueue_script( 'jq', 'https://cdn.jsdelivr.net/npm/jquery@3.6/dist/jquery.min.js', array(), '3', true );
    // Bootstrap5
    wp_enqueue_script( 'bs', 'https://unpkg.com/bootstrap@5.1/dist/js/bootstrap.min.js', array(), '5', true );
    // Swiper
    wp_enqueue_script( 'swiper', 'https://unpkg.com/swiper@7/swiper-bundle.min.js', array(), '7', true );


    // ユーザーページで動作する
    if(file_exists("{$childTheme_path}/vue-member/v-{$slug}.js")){
        wp_enqueue_script( 'g-api', "https://apis.google.com/js/api.js", array(), '', true );
        wp_enqueue_script( 'libUtility', "https://timeconcier.jp/forline/tccom/lib/js/tcLiffUtility.js", array(), date('YmdHis'), true );
        wp_enqueue_script( 'libKintone', "https://timeconcier.jp/forline/tccom/lib/js/tcKintoneApi.js", array(), date('YmdHis'), true );
        wp_enqueue_script( 'vuetify', 'https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js', array(), '2', true );
        wp_enqueue_script( 'v-sel', 'https://cdn.jsdelivr.net/npm/vue-select@3/dist/vue-select.min.js', array(), '3', true );
        wp_enqueue_script( 'v-gtable', 'https://cdn.jsdelivr.net/npm/vue-good-table@2/dist/vue-good-table.js', array(), '2', true );
        wp_enqueue_script( 'qr-gen', 'https://cdn.jsdelivr.net/npm/vue-qriously@1/dist/vue-qriously.min.js', array(), '1', true );
        wp_enqueue_script( 'v-tinymce', 'https://cdn.jsdelivr.net/npm/@tinymce/tinymce-vue@3/lib/browser/tinymce-vue.js', array(), '3', true );
        wp_enqueue_script( 'v-setting', "{$childTheme_dir}/vue-member/vue-setting.js", array(), date('YmdHis'), true );
        wp_enqueue_script( 'v-navbar', "{$childTheme_dir}/vue-member/vue-navbar.js", array(), date('YmdHis'), true );
        wp_enqueue_script( 'v-snackbar', "{$childTheme_dir}/vue-member/vue-snackbar.js", array(), date('YmdHis'), true );
        wp_enqueue_script( 'v-job-conf', "{$childTheme_dir}/vue-member/vue-job-offer-confirm.js", array(), date('YmdHis'), true );
        wp_enqueue_script( 'v-apply-plan-modal', "{$childTheme_dir}/vue-member/vue-apply-paid-plan.js", array(), date('YmdHis'), true );
        // wp_enqueue_script( 'v-cardlist', "{$childTheme_dir}/vue-member/vue-cardlist.js", array(), date('YmdHis'), true );
        wp_enqueue_script( 'v-script', "{$childTheme_dir}/vue-member/v-{$slug}.js", array(), date('YmdHis'), true );
        wp_enqueue_script( 'fn', "{$childTheme_dir}/_src/js/functions.js", array(), date('YmdHis'), true );
    } else {
        if(file_exists("{$childTheme_path}/vue-scripts/v-{$slug}.js")){
            wp_enqueue_script( 'v-script', "{$childTheme_dir}/vue-scripts/v-{$slug}.js", array(), date('YmdHis'), true );
        }

        // 全ページ共通スクリプト
        wp_enqueue_script( 'fn', "{$childTheme_dir}/_src/js/functions.js", array(), date('YmdHis'), true );
        wp_enqueue_script( 'act', "{$childTheme_dir}/_src/js/actions.js", array(), date('YmdHis'), true );
        wp_enqueue_script( 'script', "{$childTheme_dir}/_src/js/scripts.js", array(), date('YmdHis'), true );
    }

    wp_dequeue_style( 'first-font' );
    wp_dequeue_style( 'first-genericons' );
    wp_dequeue_style( 'first-normalize' );
});


/** **********************************************************
 * 表示デバイス判定
 * ******************************************************** */
function ch_device(){
    $ua = $_SERVER['HTTP_USER_AGENT'];
    if ((strpos($ua, 'Android') !== false) && (strpos($ua, 'Mobile') !== false) || (strpos($ua, 'iPhone') !== false) || (strpos($ua, 'Windows Phone') !== false)) {
        // スマホからのアクセス
        return "mobile";
    } elseif ((strpos($ua, 'Android') !== false) || (strpos($ua, 'iPad') !== false)) {
        // タブレットからのアクセス
        return "tablet";
    } elseif ((strpos($ua, 'DoCoMo') !== false) || (strpos($ua, 'KDDI') !== false) || (strpos($ua, 'SoftBank') !== false) || (strpos($ua, 'Vodafone') !== false) || (strpos($ua, 'J-PHONE') !== false)) {
        // 携帯からのアクセス
        return "old-phone";
    } else {
        // PCからのアクセス
        return "pc";
    }
}


/** **********************************************************
 * WP_Queryでカスタムフィールドとタイトルを条件にOR検索する
 * ******************************************************** */
add_action( 'pre_get_posts', function( $q ) {
    if( $title = $q->get( '_meta_or_title' ) ) {
        add_filter( 'get_meta_sql', function( $sql ) use ( $title ) {
            global $wpdb;

            // Only run once:
            static $nr = 0;
            if( 0 != $nr++ ) return $sql;

            // Modified WHERE
            $sql['where'] = sprintf(
                " AND ( %s OR %s ) ",
                $wpdb->prepare( "{$wpdb->posts}.post_title like '%s'", $title),
                mb_substr( $sql['where'], 5, mb_strlen( $sql['where'] ) )
            );

            return $sql;
        });
    }
});

/** **********************************************************
 * 郵便番号に自動でハイフン追加
 * @param String|Number $zipcode
 * @return String - ハイフン付き郵便番号
 ********************************************************** */
function insertHyphenToZipcode($zipcode) {
    //ハイフンを取り除く（空文字に置換）
    $zipcode = str_replace("-", "", $zipcode);
    //ハイフンありのフォーマットに変換
    $zipcode = substr($zipcode ,0,3) . "-" . substr($zipcode ,3);

    return $zipcode;
}
