<?php
/** ==========================================================
 * 【カテゴリー】
 *
 * ● ページカラー設定
 * ● カテゴリーオブジェクト生成
 * ● カテゴリー編集時のアクションフック（MySQL連携） - 作成・更新
 * ● カテゴリー編集時のアクションフック（MySQL連携） - 削除
 * ======================================================== */

/** **********************************************************
 * ページカラー設定
 * ******************************************************** */
function colorType($post_id){
    // 投稿に設定されているタクソノミーのタームを取得
    $headerTerms = wp_get_object_terms($post_id, 'header_category');

    if(!empty($headerTerms)){
        $term = $headerTerms[0];
    } else {
        // タクソノミーの全てのタームを取得
        $headerTerms = get_terms(['header_category'], ['get' => 'all']);
        $keyIndex = array_search("その他", array_column($headerTerms, 'name'));
        $term = $headerTerms[$keyIndex];
    }

    $term_id    = $term->term_id;
    $term_name  = $term->name;
    $term_slug  = $term->slug;

    $num        = get_field('表示順', "header_category_{$term_id}"); // HEX値 or Bootstrap
    $color_type = get_field('カラー設定', "header_category_{$term_id}"); // HEX値 or Bootstrap
    $color      = ($color_type == 'hex') ? get_field('HEX値', "header_category_{$term_id}") : get_field('Bootstrapクラス', "header_category_{$term_id}"); // HEX値 or Bootstrap
    return [
        'num'   => intval($num),
        'id'    => $term_id,
        'name'  => $term_name,
        'slug'  => $term_slug,
        'type'  => $color_type,
        'color' => $color,
    ];
}


/** **********************************************************
 * カテゴリーオブジェクト生成
 * ******************************************************** */
function categoryObj($taxSlug) {
    $args = [
        'hide_empty' => false,
        'orderby'    => 'slug',
        'order'      => 'ASC',
    ];

    $arrTerms = json_decode( json_encode( get_terms($taxSlug, $args) ), true );

    $modified = [];
    $array    = [];

    if($taxSlug == 'cities') {
    // print_r($arrTerms);
        // ACF 「人口」フィールドを基準に降順で並び替え
        foreach($arrTerms as $key => $el){
            if(!$el['parent']) {
                $modified[$el['term_id']] = $el;
            } else {
                $population = get_field( 'population', 'cities_'.$el['term_id'] );
                $modified[$population] = $el;
            }
        }

        krsort( $modified, SORT_NUMERIC );  // 降順

        // print_r($modified);
        // ksort( $modified, SORT_NUMERIC ); // 昇順
    } else { $modified = $arrTerms; }

    foreach($modified as $term){
        $term_id    = $term['term_id'];
        $term_name  = $term['name'];
        $term_slug  = $term['slug'];
        $parent_id  = $term['parent'];

        $array[] = [
            'id'     => $term_id,
            'name'   => $term_name,
            'slug'   => $term_slug,
            'parent' => $parent_id,
        ];
    }
    return $array;
}


/** **********************************************************
 * カテゴリー編集時のアクションフック（MySQL連携）
 * - 作成・更新
 * ******************************************************** */
add_action( 'create_term', 'edited_category_terms', 10, 3 );
add_action( 'edited_term', 'edited_category_terms', 10, 3 );
function edited_category_terms( $term, $tt_id, $taxonomy ) {
    $objTerm = get_term($tt_id, $taxonomy, 'ARRAY_A');
    $data  = array(
        'fields' => array(
            'term_id'  => $objTerm['term_id'],
            'parent'   => $objTerm['parent'],
            'name'     => $objTerm['name'],
            'slug'     => $objTerm['slug'],
            'taxonomy' => $taxonomy,
        )
    );

    if(strstr($taxonomy, 'cat_')){
        curlDigitownAPI('PUT', 'dt1_wp_category_master', $data);
    } else {
        switch($taxonomy){
            // 業種カテゴリー
            case 'cat_occupation': curlDigitownAPI('PUT', 'dt1_occupation_master', $data); break;
            // 職種カテゴリー
            case 'cat_industry':   curlDigitownAPI('PUT', 'dt1_industry_master', $data);   break;
            // 都道府県・市区町村カテゴリー
            case 'cities':         curlDigitownAPI('PUT', 'dt1_city_master', $data);       break;
        }
    }
}

/** **********************************************************
 * カテゴリー編集時のアクションフック（MySQL連携）
 * - 削除
 * ******************************************************** */
add_action( 'delete_term', 'deleted_category_terms', 10, 3 );
function deleted_category_terms( $term, $tt_id, $taxonomy ){
    $data = array('query' => "`taxonomy`='{$taxonomy}' AND `term_id`={$tt_id}");

    if(strstr($taxonomy, 'cat_')){
        curlDigitownAPI('DELETE', 'dt1_wp_category_master', $data);
    } else {
        switch($taxonomy){
            // 業種カテゴリー
            case 'cat_occupation': curlDigitownAPI('DELETE', 'dt1_occupation_master', $data); break;
            // 職種カテゴリー
            case 'cat_industry':   curlDigitownAPI('DELETE', 'dt1_industry_master', $data);   break;
            // 都道府県・市区町村カテゴリー
            case 'cities':         curlDigitownAPI('DELETE', 'dt1_city_master', $data);       break;
        }
    }
}


function curlDigitownAPI($method, $table, $data) {
    $curl = curl_init();
    try {
        if (in_array($method, ['get', 'GET', 'delete', 'DELETE'])) {
            $base_url = 'https://api.digital-town.jp/records/'.$table;
            $curl_opts = array(
                CURLOPT_USERPWD         => "root:Timeconcier",
                CURLOPT_CUSTOMREQUEST   => $method,
                CURLOPT_URL             => $base_url . '?' . http_build_query($data),
                CURLOPT_RETURNTRANSFER  => true,
            );
        } elseif (in_array($method, ['post', 'POST', 'put', 'PUT', 'patch', 'PATCH'])) {
            $base_url = 'https://api.digital-town.jp/record/'.$table;
            $curl_opts = array(
                CURLOPT_USERPWD         => "root:Timeconcier",
                CURLOPT_URL             => $base_url,
                CURLOPT_CUSTOMREQUEST   => $method,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_POST            => true,
                CURLOPT_POSTFIELDS      => http_build_query($data),
            );
        }
        curl_setopt_array($curl, $curl_opts);
        $response    = curl_exec($curl);
        $information = curl_getinfo($curl);
        curl_close($curl);

        $resps = json_encode(array(
            'http_code' => $information['http_code'],
            'response'  => $response,
        ));
        // echo "<script>console.log('{$resps}')</script>";
    } catch(Exception $e) {
        print_r($e);
    }
}