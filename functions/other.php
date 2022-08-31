<?php
/** ==========================================================
 * 【その他関数】
 *
 * ● 現在のURLを取得
 * ● URLクエリパラメータを取得
 * ● ページネーション
 * ● ページネーションをBootstrapのスタイルに変更
 * ● 電話番号ハイフン表示
 * ● 郵便番号ハイフン表示
 * ● メディアにアップロードした画像URLから画像IDを取得
 * ======================================================== */

/** **********************************************************
 * 現在のURLを取得
 * ******************************************************** */
function get_current_url($after = '', $query = true) {
    if($query === true){
        return (is_ssl() ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . $after;
    } else {
        $req_uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
        return (is_ssl() ? 'https' : 'http') . '://' . $_SERVER["HTTP_HOST"] . $req_uri . $after;
    }
}


/** **********************************************************
 * URLクエリパラメータを取得
 * ******************************************************** */
function getParams($url = null){
    if(!$url){
        $url = get_current_url();
    }
    $pattern = "/[\?&]([^&]+)=([^&#]+)/";
    preg_match_all($pattern, $url, $matches);

    $queries = [];
    $size = count($matches[0]);
    for($i = 0; $i < $size; $i++){
        $key = $matches[1][$i];
        $value = $matches[2][$i];
        $queries[$key] = $value;
    }
    return $queries;
}


/** **********************************************************
 * ページネーション
 * ******************************************************** */
function pagenation($pages = '', $range = 2){
    $showitems = ($range * 1)+1;
    global $paged;
    if(empty($paged)) $paged = 1;
    if($pages == ''){
        global $wp_query;
        $pages = $wp_query->max_num_pages;
        if(!$pages){
            $pages = 1;
        }
    }
    if(1 != $pages){
        // 画像を使う時用に、テーマのパスを取得
        $img_pass = get_template_directory_uri();
        echo '<div class="m-pagenation">';
        // 「1/2」表示 現在のページ数 / 総ページ数
        // echo "<div class=\"m-pagenation__result\">". $paged."/". $pages."</div>";
        // 「前へ」を表示
        // if($paged > 1) echo "<div class=\"m-pagenation__prev\"><a href='".get_pagenum_link($paged - 1)."'>前へ</a></div>";
        // ページ番号を出力
        echo '<ol class="m-pagenation__body">';
        for ($i=1; $i <= $pages; $i++){
            if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )){
                echo ($paged == $i) ? '<li class="-current">'.$i.'</li>': // 現在のページの数字はリンク無し
                    '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
            }
        }
        // [...] 表示
        // if(($paged + 4 ) < $pages){
        //     echo "<li class=\"notNumbering\">...</li>";
        //     echo "<li><a href='".get_pagenum_link($pages)."'>".$pages."</a></li>";
        // }
        echo '</ol>';
        // 「次へ」を表示
        // if($paged < $pages) echo "<div class=\"m-pagenation__next\"><a href='".get_pagenum_link($paged + 1)."'>次へ</a></div>";
        echo '</div>';
    }
}

/** **********************************************************
 * ページネーションをBootstrapのスタイルに変更
 * ******************************************************** */
function custom_pagination ($post_query) {
    global $_GET;
    $site_url       = get_bloginfo('url');
    $this_post_type = (is_search()) ? '' : $post_query->query['post_type'].'/';
    $this_page_num  = $post_query->query['paged'];
    $max_page_num   = ($post_query->max_num_pages) ? $post_query->max_num_pages : 1;

    $prev_page = ($this_page_num == 1) ? 'disabled' : '';
    $next_page = ($this_page_num == $max_page_num) ? 'disabled' : '';

    $prev_page_num  = $this_page_num - 1;
    $next_page_num  = $this_page_num + 1;

    $q = '';
    if(!empty($_GET)) $q = '?'.http_build_query($_GET);

$paginate = <<<EOD
<div class="d-flex flex-wrap justify-content-between align-items-stretch mb-4">
    <div class="prev-buttons d-flex gap-1">
        <a href="{$site_url}/{$this_post_type}{$q}" class="btn btn-dark d-flex gap-2 align-items-center {$prev_page}">
            <i class="fas fa-angle-double-left"></i>
            <span class="d-none d-sm-inline">最初へ</span>
        </a>
        <a href="{$site_url}/{$this_post_type}page/{$prev_page_num}{$q}" class="btn btn-dark d-flex gap-2 align-items-center {$prev_page}">
            <i class="fas fa-angle-left"></i>
            <span class="d-none d-sm-inline">前へ</span>
        </a>
    </div>

    <div class="border border-dark d-flex align-items-center px-2">
        <span>{$this_page_num}<span class="mx-1">/</span>{$max_page_num}</span>
    </div>

    <div class="next-buttons d-flex gap-1">
        <a href="{$site_url}/{$this_post_type}page/{$next_page_num}{$q}" class="btn btn-dark d-flex gap-2 align-items-center {$next_page}">
            <span class="d-none d-sm-inline">次へ</span>
            <i class="fas fa-angle-right"></i>
        </a>
        <a href="{$site_url}/{$this_post_type}page/{$max_page_num}{$q}" class="btn btn-dark d-flex gap-2 align-items-center {$next_page}">
            <span class="d-none d-sm-inline">最後へ</span>
            <i class="fas fa-angle-double-right"></i>
        </a>
    </div>
</div>
EOD;

    return $paginate;
}


/** **********************************************************
 * 電話番号ハイフン表示
 * ******************************************************** */
function phoneNumberFormat($input) {
    $input = str_replace("-", "", $input);

	//変数宣言
	$category = array(
        "normal" => "/^0[^346]\d{8}$/",
        "mobile" => "/^\d{11}$/",
        "tokyo"  => "/^0[346]\d{7}$/",
        "none"   => "/^\d{7}$/",
	);
	$pattern = array(
        "normal" => "/(\d{3})(\d{3})(\d{4})/",
        "mobile" => "/(\d{3})(\d{4})(\d{4})/",
        "tokyo"  => "/(\d{2})(\d{3})(\d{4})/",
        "none"   => "/(\d{3})(\d{4})/",
	);
	$rep = array(
        "normal" => "$1-$2-$3",
        "none"   => "$1-$2",
	);

	//携帯なら
	if(preg_match($category['mobile'],$input)) {
		$result = preg_replace($pattern['mobile'],$rep['normal'],$input);
	}
	//市外局番2桁なら
	elseif(preg_match($category['tokyo'],$input)) {
		$result = preg_replace($pattern['tokyo'],$rep['normal'],$input);
	}
	//普通の市外局番なら
	elseif(preg_match($category['normal'],$input)) {
		$result = preg_replace($pattern['normal'],$rep['normal'],$input);
	}
	//市外局番なしなら
	elseif(preg_match($category['none'],$input)) {
		$result = preg_replace($pattern['none'],$rep['none'],$input);
	}
	//その他なら
	else {
			$result = $input;
	}

	return $result;
}


/** **********************************************************
 * 郵便番号ハイフン表示
 * ******************************************************** */
function zipCodeFormat($input) {
    $input = str_replace("-", "", $input);
    return substr($input, 0, 3).'-'.substr($input, 3);
}


/** **********************************************************
 * メディアにアップロードした画像URLから画像IDを取得
 * ******************************************************** */
function get_attachment_id_from_url($url) {
    global $wpdb, $table_prefix;
    $query = "SELECT ID FROM {$table_prefix}posts WHERE guid='{$url}'";
    $id = $wpdb->get_var($query);
    return $id;
}
