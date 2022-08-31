<?php
/** ==========================================================
 * 【管理画面】
 *
 * ● 「管理者」以外の権限のダッシュボードへのアクセスを禁止する
 * ● 管理者以外、Adminバーを非表示
 * ● ユーザー編集画面に表示する項目を制御
 * ● ユーザー一覧にカスタムフィールドを追加
 * ● デフォルトソート順変更
 * ● ユーザー検索機能を拡張
 * ● メール配信停止
 * ● メール配信設定 - SMTP TLS化
 * ● ユーザー作成時、作成したユーザーと管理者へメール送信
 * ======================================================== */
/** **********************************************************
 * 「管理者」以外の権限のダッシュボードへのアクセスを禁止する
 * - 管理者、wp_ajax処理のみ許可
 * - リダイレクト先：トップページ
 ********************************************************** */
add_action('admin_init', function () {
    $home = home_url();
    if(get_current_user_id() != 1 and get_current_user_id() != 1312 and !wp_doing_ajax()) {
        header("Location: {$home}");
        exit;
    }
});


/** **********************************************************
 * 管理画面
 * - メニュー項目削除
 ********************************************************** */
add_action( 'admin_menu', function () {
    if(get_current_user_id() == 1) return;

    remove_menu_page( 'index.php' ); // ダッシュボード.
    remove_menu_page( 'edit.php' ); // 投稿.
    // remove_menu_page( 'upload.php' ); // メディア.
    // remove_menu_page( 'edit.php?post_type=page' ); // 固定.
    remove_menu_page( 'edit-comments.php' ); // コメント.
    remove_menu_page( 'themes.php' ); // 外観.
    // remove_menu_page( 'plugins.php' ); // プラグイン.
    // remove_menu_page( 'users.php' ); // ユーザー.
    remove_menu_page( 'tools.php' ); // ツール.
    // remove_menu_page( 'options-general.php' ); // 設定.
}, 999 );


/** **********************************************************
 * ユーザー編集画面に表示する項目を制御
 ********************************************************** */
add_action('admin_enqueue_scripts', function () {
    $script = <<<SCRIPT
jQuery(function($) {
  jQuery('#your-profile h2').hide();    // ヘッダー
  jQuery('#your-profile .user-rich-editing-wrap').hide();           // ビジュアルエディター
  jQuery('#your-profile .user-syntax-highlighting-wrap').hide();    // シンタックスハイライト
  jQuery('#your-profile .user-admin-color-wrap').hide();            // 管理画面の配色
  jQuery('#your-profile .user-comment-shortcuts-wrap').hide();      // キーボードショートカット
  jQuery('#your-profile .show-admin-bar').hide();                   // ツールバー
//   jQuery('#your-profile .user-language-wrap').hide();               // 言語
  jQuery('#your-profile .user-last-name-wrap').hide();              // 姓
  jQuery('#your-profile .user-first-name-wrap').hide();             // 名
  jQuery('#your-profile .user-url-wrap').hide();                    // サイト
  jQuery('#your-profile .user-description-wrap').hide();            // プロフィール情報
});
SCRIPT;
    wp_add_inline_script( 'jquery-core', $script );
});


/** **********************************************************
 * 管理者以外、Adminバーを非表示
 ********************************************************** */
add_filter( 'show_admin_bar' , function($content) {
    return false;
});


/** **********************************************************
 * ユーザー一覧にカスタムフィールドを追加
 * add_users_custom_column  - 表示するフィールドを追加
 * add_users_columns        - 表示するフィールドと順番を指定
 * ******************************************************** */
add_filter( 'manage_users_columns', 'add_users_columns' );
add_filter( 'manage_users_custom_column', 'add_users_custom_column', 10, 3 );
// 表示するフィールドと順番を指定
function add_users_columns( $columns ) {
	//権限のパラメーターがあった場合とない場合の設定
	$role = (!empty($_GET['role'])) ? '?role='.$_GET['role'].'&' : '?';
	//リンクのパラメーターが交互になるように設定
	$order = ($_GET['order'] == "desc") ? 'asc' : 'desc';

    $columns['enterprise']   = '事業所名';
    $columns['registered']   = '<a href="./users.php'.$role.'orderby=user_registered&order='.$order.'">登録日</a>';
    $columns['contact_name'] = '担当者';
    unset( $columns['email'], $columns['name'], $columns['posts'] );
    $sort_number = array(
        'username'     => 0,  // ユーザー名
        // 'name'       => 1,  // メールアドレス
        // 'posts'      => 3,  // 投稿
        'enterprise'   => 1,  // 事業所名
        'contact_name' => 2,  // 担当者
        // 'email'        => 2,  // メールアドレス
        'role'         => 3,  // 権限グループ
        'registered'   => 4,  // 登録日
    );
    $sort = array();
    foreach($columns as $key => $value){
        $sort[] = $sort_number[$key];
    }
    array_multisort($sort,$columns);
    return $columns;
}

// 表示するフィールドを追加
function add_users_custom_column( $dummy, $column, $user_id ) {
    if ( $column == 'enterprise' ) {
        $user_info = get_userdata($user_id);
        // print_r($user_info);
        return $user_info->display_name;
    }
    if ( $column == 'registered' ) {
        $user_info = get_userdata($user_id);
        return date('Y年n月j日 G時i分s秒', strtotime("{$user_info->user_registered} +9 hour"));
    }
    if ( $column == 'contact_name' ) {
        $user_info = get_the_author_meta('担当者名', $user_id);
        return ($user_info) ? $user_info : '【未設定】';
    }
}

/** **********************************************************
 * デフォルトソート順変更
 ********************************************************** */
add_action('pre_user_query', 'fn_pre_user_query');
function fn_pre_user_query($query) {
    //// デフォルトでは新しい順に表示させる ////
    global $pagenow;
    if (is_admin() && $pagenow === 'users.php' && ! isset($_GET['orderby'])) {
        $query->query_orderby = 'ORDER BY user_registered DESC';
    }
}

/** **********************************************************
 * ユーザー検索機能を拡張
 * - ユーザー一覧をカスタムフィールドも検索対象にする
 * ******************************************************** */
add_action('pre_user_query', 'extended_user_search');
function extended_user_search($user_query) {
    // Make sure this is only applied to user search
    if ( $user_query->query_vars['search'] ){
        $search = trim( $user_query->query_vars['search'], '*' );
        if ( $_REQUEST['s'] == $search ){
            global $wpdb;

            $keys = array( 'user_login', 'user_email', 'user_nicename' );
            $newKeys = array( 'first_name', 'last_name', 'nickname', '担当者名' );

            foreach($newKeys as $i => $key) {
                $i = $i + 1;
                $keys[] = "UM{$i}.meta_value";
                $user_query->query_from .= " JOIN {$wpdb->usermeta} as UM{$i} ON UM{$i}.user_id = {$wpdb->users}.ID AND UM{$i}.meta_key = '{$key}'";
            }

            $user_query->query_where = 'WHERE 1=1' . $user_query->get_search_sql( $search, $keys, 'both' );
        }
    }
}

/** **********************************************************
 * メール配信停止
 * ******************************************************** */
add_filter( 'wp_new_user_notification_email_admin', '__return_false' );     // ユーザー登録時の管理メールアドレス宛メール送信停止
add_filter( 'wp_new_user_notification_email', '__return_false' );           // ユーザー登録時の登録ユーザー宛メール送信停止
add_filter( 'send_email_change_email', '__return_false' );                  // メールアドレス変更時のメール送信停止
add_filter( 'send_password_change_email', '__return_false' );               // パスワード変更時のメール送信停止
add_filter( 'wp_password_change_notification_email', '__return_false' );    // パスワードリセット時のメール送信停止


/** **********************************************************
 * メール配信設定
 * - SMTP TLS化
 * ******************************************************** */
add_action( 'phpmailer_init', function ( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Host       = SMTP_HOST;
    $phpmailer->Port       = SMTP_PORT;  //587 or 465
    $phpmailer->Username   = SMTP_USERNAME;
    $phpmailer->Password   = SMTP_PASSWORD;
    $phpmailer->From       = SMTP_USERNAME;
    $phpmailer->FromName   = 'デジタル町一丁目';
    $phpmailer->SMTPSecure = 'tls';  //tls or ssl

    $jsonData = json_encode($phpmailer, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE).PHP_EOL;
    file_put_contents(dirname(__DIR__).'/log/user_regist.log', $jsonData, FILE_APPEND);
});

