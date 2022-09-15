<?php
$post_type  = $args['post_type'];
$params     = $args['params'];

$post_type_name = str_replace('情報', '', get_post_type_object($post_type)->label);

$today  = date('Y-m-d');
$paged  = get_query_var('paged') ? get_query_var('paged') : 1;
/** ********************************************************************
 * 絞り込み設定
 ******************************************************************** */
$args   = array(
  'posts_per_page' => 20,
  'post_type'      => $post_type,
  'post_status'    => 'publish',
  'paged'          => $paged,
  'tax_query'      => array(
    'relation' => 'AND',
  ),
  'orderby'        => array(),
);

/** ********************************************************************
 * ログインユーザーが以下以外の場合、TC,ACユーザーの投稿を非表示にする
 ******************************************************************** */
$current_user = get_current_user_id();
$manager_user = array(1, 2, 27, 764);
if (!in_array($current_user, $manager_user)) $args['author__not_in'] = $manager_user;

/** **************************************************************
 * ============================================================= *
 * 【絞り込み条件】
 *                         検索ページ
 * ============================================================= *
 ************************************************************** */
if (is_search()) {
  $search_query           = str_replace('　', ' ', get_search_query());
  $arr_search             = explode(' ', $search_query);
  $args['_meta_or_title'] = $search_query;
  $args['meta_query']     = [];


  switch ($post_type) {
      /** ****************************************************************
     * 事業者一覧取得用クエリ
     **************************************************************** */
    case 'enterprises':
      $args['meta_query']         = array('relation' => 'AND');
      $args['meta_query']['word'] = array('relation' => 'OR');

      foreach ($arr_search as $word) {
        if (empty($word)) continue;
        // 文字列検索
        $args['meta_query']['word'][] = array('key' => '概要',           'value' => $word, 'compare' => 'LIKE');
        $args['meta_query']['word'][] = array('key' => '検索キーワード', 'value' => $word, 'compare' => 'LIKE');
      }

      // 入力達成率
      $args['meta_query']['rate'] = array(
        'key'  => '入力達成率',
        'type' => 'NUMERIC'
      );

      $args['orderby']['rate']     = 'DESC';
      $args['orderby']['modified'] = 'DESC';
      // print_r($args);
      break;

      /** ****************************************************************
       * 求人一覧取得用クエリ
       **************************************************************** */
    case 'job_offers':
      $args['tax_query']          = array('relation' => 'OR');
      $args['meta_query']         = array('relation' => 'AND');
      $args['meta_query']['word'] = array('relation' => 'OR');

      foreach ($arr_search as $word) {
        if (empty($word)) continue;
        // 文字列検索
        $args['meta_query']['word'][] = array('key' => '事業所名',     'value' => $word, 'compare' => 'LIKE');
        $args['meta_query']['word'][] = array('key' => '住所',         'value' => $word, 'compare' => 'LIKE');
        $args['meta_query']['word'][] = array('key' => '電話番号',     'value' => $word, 'compare' => 'LIKE');
        $args['meta_query']['word'][] = array('key' => '雇用補足',     'value' => $word, 'compare' => 'LIKE');
        $args['meta_query']['word'][] = array('key' => '給与補足',     'value' => $word, 'compare' => 'LIKE');
        $args['meta_query']['word'][] = array('key' => '勤務時間補足', 'value' => $word, 'compare' => 'LIKE');
        $args['meta_query']['word'][] = array('key' => '休日補足',     'value' => $word, 'compare' => 'LIKE');
        $args['meta_query']['word'][] = array('key' => '仕事内容',     'value' => $word, 'compare' => 'LIKE');
        $args['meta_query']['word'][] = array('key' => 'メッセージ',   'value' => $word, 'compare' => 'LIKE');
      }

      // if($arr_search) {
      //     // タグ検索
      //     $tag_taxonomies = [
      //         'cat_job_offer_genre',
      //         'cat_job_offer_genre_agriculture',
      //         'cat_job_offer_genre_wedding',
      //         'cat_job_offer_genre_stay'
      //     ];
      //     foreach($tag_taxonomies as $taxonomy) {
      //         $args['tax_query'][] = array(
      //             'taxonomy' => $taxonomy,
      //             'field'    => 'slug',
      //             'terms'    => $arr_search,
      //         );
      //     }
      // }

      // 給与形態：$params['f_sly']
      // 給与(最低)：$params['f_amt']
      if ($params['f_sly'] and $params['f_amt']) {
        $args['meta_query'][] = array('key' => '給与形態', 'compare' => '=',  'value' => $params['f_sly']);
        $args['meta_query'][] = array('key' => '給与_自_', 'compare' => '>=', 'value' => $params['f_amt'], 'type' => 'NUMERIC');
      }

      // 勤務開始：$param['f_time_start']
      // 勤務終了：$param['f_time_end']
      $args['meta_query']['period'] = array('relation' => 'AND');
      if ($params['f_time_start'] and $params['f_time_end']) {
        $args['meta_query']['period'][] = array('key' => '就労時間_自_', 'compare' => '<=', 'value' => $params['f_time_start'], 'type' => 'TIME');
        $args['meta_query']['period'][] = array('key' => '就労時間_至_', 'compare' => '>=', 'value' => $params['f_time_end'],   'type' => 'TIME');
      } elseif ($params['f_time_start'] and !$params['f_time_end']) {
        $args['meta_query']['period'][] = array('key' => '就労時間_自_', 'compare' => '<=', 'value' => $params['f_time_start'], 'type' => 'TIME');
        $args['meta_query']['period'][] = array('key' => '就労時間_至_', 'compare' => '>=', 'value' => $params['f_time_start'], 'type' => 'TIME');
      } elseif (!$params['f_time_start'] and $params['f_time_end']) {
        $args['meta_query']['period'][] = array('key' => '就労時間_自_', 'compare' => '<=', 'value' => $params['f_time_end'],   'type' => 'TIME');
        $args['meta_query']['period'][] = array('key' => '就労時間_至_', 'compare' => '>=', 'value' => $params['f_time_end'],   'type' => 'TIME');
      } else {
        unset($args['meta_query']['period']);
      }


      if ($params['f_emp']) {
        // print_r($params);
        $args['meta_query']['emp'] = array('relation' => 'OR');
        foreach ($params['f_emp'] as $emp) {
          $args['meta_query']['emp'][] = array('key' => '契約形態', 'value' => $emp, 'compare' => 'IN');
        }
      }

      $args['orderby']['modified'] = 'DESC';
      break;

      /** ****************************************************************
       * イベント一覧取得用クエリ
       **************************************************************** */
    case 'events':
      $args['tax_query']          = array('relation' => 'OR');
      $args['meta_query']         = array('relation' => 'AND');
      $args['meta_query']['word'] = array('relation' => 'OR');

      foreach ($arr_search as $word) {
        if (empty($word)) continue;
        $args['meta_query']['word'][] = array(
          'relation' => 'OR',
          array('key' => 'イベント情報',  'value' => $word,  'compare' => 'LIKE'),
          array('key' => '開催日時詳細',  'value' => $word,  'compare' => 'LIKE'),
          array('key' => '開催場所',      'value' => $word,  'compare' => 'LIKE'),
          array('key' => '交通アクセス',  'value' => $word,  'compare' => 'LIKE'),
          array('key' => 'お問い合わせ',  'value' => $word,  'compare' => 'LIKE'),
        );
      }

      if ($params['l']) {
        $cityTerm = get_term_by('slug', $params['l'], 'cities');
        $args['meta_query'][] = array('key' => '開催場所', 'value' => $cityTerm->name, 'compare' => 'LIKE');
      }

      // 駐車場有無
      if ($params['f_ev_p']) {
        $args['meta_query'][] = array(
          'key'     => '駐車場有無',
          'compare' => '=',
          'value'   => $params['f_ev_p'],
        );
      }

      // 開催期間
      $args['meta_query']['period'] = array('relation' => 'OR');
      if (isset($params['f_ev_start']) and isset($params['f_ev_end'])) {
        if ($params['f_ev_start'] and $params['f_ev_end']) {
          $args['meta_query']['period'] = array('relation' => 'AND');
          $args['meta_query']['period'][] = array('key' => '開催日_自_', 'compare' => '<=', 'value' => $params['f_ev_start'], 'type' => 'DATE');
          $args['meta_query']['period'][] = array('key' => '開催日_至_', 'compare' => '<=', 'value' => $params['f_ev_end'],   'type' => 'DATE');
        } elseif ($params['f_ev_start'] and !$params['f_ev_end']) {
          $args['meta_query']['period'][] = array('key' => '開催日_自_', 'compare' => '>=', 'value' => $params['f_ev_start'], 'type' => 'DATE');
          $args['meta_query']['period'][] = array('key' => '開催日_至_', 'compare' => '>=', 'value' => $params['f_ev_start'], 'type' => 'DATE');
        } elseif (!$params['f_ev_start'] and $params['f_ev_end']) {
          $args['meta_query']['period'][] = array('key' => '開催日_自_', 'compare' => '>=', 'value' => $params['f_ev_end'], 'type' => 'DATE');
          $args['meta_query']['period'][] = array('key' => '開催日_至_', 'compare' => '<=', 'value' => $params['f_ev_end'], 'type' => 'DATE');
        }
      } else {
        unset($args['meta_query']['period']);
        $args['meta_query']['period'] = array('relation' => 'AND');
        $args['meta_query']['period'][] = array('key' => '開催日_至_', 'compare' => '>=', 'value' => date('Y-m-d'), 'type' => 'DATE');
      }


      $args['orderby']['modified'] = 'DESC';
      break;

      /** ****************************************************************
       * クーポン一覧取得用クエリ
       **************************************************************** */
    case 'coupons':
      $args['meta_query']          = array('relation' => 'AND');
      $args['meta_query']['word']  = array('relation' => 'OR');
      $args['orderby']['modified'] = 'DESC';

      foreach ($arr_search as $word) {
        $args['meta_query']['word'][] = array(
          'relation' => 'OR',
          array('key' => '事業所名',   'value' => $word, 'compare' => 'LIKE'),
          array('key' => '都道府県名', 'value' => $word, 'compare' => 'LIKE'),
          array('key' => '住所',       'value' => $word, 'compare' => 'LIKE'),
          array('key' => '定価',       'value' => $word, 'compare' => 'LIKE'),
          array('key' => '割引値',     'value' => $word, 'compare' => 'LIKE'),
          array('key' => '割引種別',   'value' => $word, 'compare' => 'LIKE'),
          array('key' => '店舗名',     'value' => $word, 'compare' => 'LIKE'),
          array('key' => '条件',       'value' => $word, 'compare' => 'LIKE'),
          array('key' => '条件詳細',   'value' => $word, 'compare' => 'LIKE'),
        );
      }

      // 利用開始日
      if (isset($params['f_cp_start']) or isset($params['f_cp_end'])) {
        $f_cp_start = isset($params['f_cp_start']) ? $params['f_cp_start'] : null;
        $f_cp_end   = isset($params['f_cp_end'])   ? $params['f_cp_end']   : null;

        $period_meta_query = array(
          'relation' => 'OR',
        );

        if ($f_cp_start and $f_cp_end) {
          $period_meta_query[] = array(
            'relation' => 'AND',
            array('key' => '利用開始日', 'value' => date('Y/m/d', strtotime($params['f_cp_start'])), 'compare' => '<=', 'type' => 'DATE'),
            array('key' => '利用終了日', 'value' => date('Y/m/d', strtotime($params['f_cp_start'])), 'compare' => '>=', 'type' => 'DATE'),
          );
          $period_meta_query[] = array(
            'relation' => 'AND',
            array('key' => '利用開始日', 'value' => '', 'compare' => '='),
            array('key' => '利用終了日', 'value' => '', 'compare' => '='),
          );
        } elseif ($f_cp_start and !$f_cp_end) {
          $period_meta_query[] = array('key' => '利用開始日', 'value' => date('Y/m/d', strtotime($params['f_cp_start'])), 'compare' => '<=', 'type' => 'DATE');
          $period_meta_query[] = array('key' => '利用開始日', 'value' => '', 'compare' => '=');
        } elseif (!$f_cp_start and $f_cp_end) {
          $period_meta_query[] = array('key' => '利用終了日', 'value' => date('Y/m/d', strtotime($params['f_cp_start'])), 'compare' => '>=', 'type' => 'DATE');
          $period_meta_query[] = array('key' => '利用終了日', 'value' => '', 'compare' => '=');
        } else {
          break;
        }

        $args['meta_query'][] = $period_meta_query;
      }

      break;


      /** ****************************************************************
       * 標準クエリ
       **************************************************************** */
      case 'post':
        $args['exact'] = false;
        $args['s'] = $search_query;
        break;
  }

  foreach ($params as $key => $prm) {
    if ($key == 'pt' || $key == 's') continue;
    if ($prm) {
      switch ($key) {
        case 'c':
          $key = 'category';
          $fld = 'slug';
          break;  // 標準カテゴリ
        case 'l':
          $key = 'cities';
          $fld = 'slug';
          break;  // 地域カテゴリ
        case 'lg':
          $key = 'header_category';
          $fld = 'name';
          break;  // 事業者・求人の大カテゴリ
        case 'jt':
          $key = 'cat_job_type';
          $fld = 'name';
          break;  // 職種カテゴリ
        case 'ev':
          $key = 'cat_events';
          $fld = 'name';
          break;  // イベントカテゴリ
        case 'cp':
          $key = 'cat_coupon';
          $fld = 'name';
          break;  // クーポンカテゴリ
        case 'g_ep':
          $key = 'cat_enterprise_genre';
          $fld = 'name';
          break;  // 事業所ジャンル
        case 'g_jo':
          $key = 'cat_job_offer_genre';
          $fld = 'name';
          break;  // 求人票ジャンル（共通）
        case 'g_jo_agr':
          $key = 'cat_job_offer_genre_agriculture';
          $fld = 'name';
          break;  // 求人票ジャンル（農業）
        case 'g_jo_wed':
          $key = 'cat_job_offer_genre_wedding';
          $fld = 'name';
          break;  // 求人票ジャンル（ウェディング）
        case 'g_jo_sty':
          $key = 'cat_job_offer_genre_stay';
          $fld = 'name';
          break;  // 求人票ジャンル（宿泊）
        case 'tag':
          $key = 'cat_'.$post_type;
          $fld = 'name';
          break;  // 求人票ジャンル（宿泊）
        case 't_prt':
          $key = 'tag_parentings';
          $fld = 'slug';
          break;  // 子育てタグ
        case 't_rcp':
          $key = 'tag_recipes';
          $fld = 'slug';
          break;  // レシピタグ
        default:
          $key = null;
          break;  // その他
      }


      if ($key and $fld) {
        $args['tax_query'][] = [
          'taxonomy' => $key,
          'field'    => $fld,
          'terms'    => $prm,
          'operator' => 'IN',
        ];
      }

    }
  }
}

/** **************************************************************
 * ============================================================= *
 * 【絞り込み条件】
 *                      アーカイブページ
 * ============================================================= *
 ************************************************************** */
elseif (is_post_type_archive()) {
  $args['tax_query']['relation'] = 'AND';
  switch ($post_type) {
      /** ****************************************************************
     * 事業者一覧取得用クエリ
     * - 達成率: 降順
     * - 更新日: 降順
     **************************************************************** */
    case 'enterprises':
      // 入力達成率
      $args['meta_query']['rate'] = array(
        'key'  => '入力達成率',
        'type' => 'NUMERIC'
      );
      $args['orderby']['rate']     = 'DESC';
      $args['orderby']['modified'] = 'DESC';
      break;

      /** ****************************************************************
       * 求人一覧取得用クエリ
       * - 更新日: 降順
       **************************************************************** */
    case 'job_offers':
      // ヘッダーカテゴリーが指定されていた場合
      $args['orderby']['modified'] = 'DESC';
      break;

      /** ****************************************************************
       * イベント一覧取得用クエリ
       * - 更新日: 降順
       **************************************************************** */
    case 'events':
      $args['meta_query'] = array();


      // 開催期間
      $args['meta_query']['period'] = array('relation' => 'OR');
      if (isset($params['f_ev_start']) and isset($params['f_ev_end'])) {
        if ($params['f_ev_start'] and $params['f_ev_end']) {
          $args['meta_query']['period'] = array('relation' => 'AND');
          $args['meta_query']['period'][] = array('key' => '開催日_自_', 'compare' => '<=', 'value' => $params['f_ev_start'], 'type' => 'DATE');
          $args['meta_query']['period'][] = array('key' => '開催日_至_', 'compare' => '<=', 'value' => $params['f_ev_end'],   'type' => 'DATE');
        } elseif ($params['f_ev_start'] and !$params['f_ev_end']) {
          $args['meta_query']['period'][] = array('key' => '開催日_自_', 'compare' => '>=', 'value' => $params['f_ev_start'], 'type' => 'DATE');
          $args['meta_query']['period'][] = array('key' => '開催日_至_', 'compare' => '>=', 'value' => $params['f_ev_start'], 'type' => 'DATE');
        } elseif (!$params['f_ev_start'] and $params['f_ev_end']) {
          $args['meta_query']['period'][] = array('key' => '開催日_自_', 'compare' => '<=', 'value' => $params['f_ev_end'], 'type' => 'DATE');
          $args['meta_query']['period'][] = array('key' => '開催日_至_', 'compare' => '<=', 'value' => $params['f_ev_end'], 'type' => 'DATE');
        }
      } else {
        unset($args['meta_query']['period']);
        $args['meta_query']['period'] = array('relation' => 'AND');
        $args['meta_query']['period'][] = array('key' => '開催日_至_', 'compare' => '>=', 'value' => date('Y-m-d'), 'type' => 'DATE');
      }


      // ヘッダーカテゴリーが指定されていた場合
      if (isset($params['ev']) && !empty($params['ev'])) {
        $cat_ev = (is_array($params['ev'])) ? $params['lg'] : explode(',', $params['ev']);
        $args['tax_query'][] = [
          'taxonomy' => 'cat_events',
          'field'    => 'slug',
          'terms'    => $cat_ev,
        ];
      }
      $args['orderby']['modified'] = 'DESC';
      break;

      /** ****************************************************************
       * クーポン一覧取得用クエリ
       * - 更新日: 降順
       **************************************************************** */
    case 'coupons':
      // ヘッダーカテゴリーが指定されていた場合
      if (isset($params['cp']) && !empty($params['cp'])) {
        $cat_cp = (is_array($params['cp'])) ? $params['lg'] : explode(',', $params['cp']);
        $args['tax_query'][] = [
          'taxonomy' => 'cat_coupon',
          'field'    => 'slug',
          'terms'    => $cat_cp,
        ];
      }
      $args['orderby']['modified'] = 'DESC';
      break;
  }

  // 市町村が指定されていた場合
  if (isset($params['l']) && !empty($params['l'])) {
    $city = (is_array($params['l'])) ? $params['l'] : explode(',', $params['l']);
    $args['tax_query'][] = [
      'taxonomy' => 'cities',
      'field'    => 'slug',
      'terms'    => $city,
      'include_children' => true,
      'operator' => 'IN'
    ];
  }
}

// print_r($args);

$the_query = new WP_Query($args);
// print_r($the_query);
?>
<div class="d-flex justify-content-between align-items-end w-100 mb-3 px-2">
  <small class="d-block">
    <?php if (!empty($search_query)) : ?>
      <div>検索結果：<?= $search_query; ?></div>
    <?php endif; ?>
  </small>
  <?php
  $posts_per_page = $the_query->query['posts_per_page'];
  $this_page      = $the_query->query['paged'];
  $this_page_min_post = $posts_per_page * ($this_page - 1) + 1;
  $this_page_max_post = $posts_per_page * ($this_page - 1) + $the_query->post_count;
  ?>
  <?php if ($the_query->found_posts) : ?>
    <small class="text-end" style="min-width: 60px;">
      <?= $this_page_min_post . '～' . $this_page_max_post; ?>件 / 全<?= $the_query->found_posts; ?>件
    </small>
  <?php endif; ?>
</div>


<div class="container">
  <div class="row">

    <?php $i = 0;
    if ($the_query->have_posts()) : while ($the_query->have_posts()) : $the_query->the_post(); ?>
        <?php
        $i++;
        $post_id   = get_the_ID();
        $thumnbnal = get_the_post_thumbnail_url($post_id);
        // $thumnbnal = ($thumnbnal) ? $thumnbnal : wp_get_attachment_url(NO_IMAGE_ID);
        // $hCat = colorType($post_id);
        if ($hCat['type'] == 'bs_class') {
          $bg_color_bs  = "bg-{$hCat['color']}";
          $bg_color_hex = '';
        } else {
          $bg_color_bs  = '';
          $bg_color_hex = "background-color:{$hCat['color']}";
        }

        $epId       = get_field('事業所id');
        $epName     = get_the_author_meta('display_name', $epId);
        $epTel      = get_the_author_meta('電話番号', $epId);
        $epOutLink  = get_the_author_meta('外部リンクを使用', $epId);
        $epFullAdrs = get_the_author_meta('都道府県名', $epId) . get_the_author_meta('住所1_市名_', $epId) . get_the_author_meta('住所2', $epId);
        $epUrl      = get_the_author_meta('サイトurl', $epId);

        $permaUrl   = get_permalink();
        ?>

        <?php
        // ========================================================================
        // 事業所検索
        // ------------------------------------------------------------------------
        if ($post_type == 'enterprises' and get_the_title()) :
          $genre = get_the_terms($post_id, 'cat_enterprise_genre');
          $permaUrl   = ($epOutLink and $epUrl) ? $epUrl : $permaUrl;
          $ep_kintone_rec_id = get_the_author_meta('rec_id', $epId);
          // echo get_field('入力達成率');
          // ======================================================================== 
        ?>
          <div class="col-12 col-md-6 col-lg-4">
            <div class="d-flex justify-content-between align-items-center bg-primary p-2 gap-1">
              <a href="<?= $permaUrl; ?>" class="fw-bold text-white"><?= the_title(); ?></a>
              <?php if ($ep_kintone_rec_id) : ?>
                <a class="d-sm-none" href="http://line.me/R/msg/text/?「デジタル町一丁目」LINE公式アカウントを今すぐ登録！%0D%0Ahttps://liff.line.me/1655895861-jrygqoZw?corp=<?= $ep_kintone_rec_id; ?>">
                  <img style="width:30px;" src="<?= get_stylesheet_directory_uri() . '/_src/icon/line.svg'; ?>" alt="">
                </a>
              <?php endif; ?>
            </div>
            <div class="bg-white mb-3 w-100 border border-primary border-top-0">
              <div class="d-sm-flex">
                <div class="d-flex">
                  <a href="<?= $permaUrl; ?>" class="d-block">
                    <?= the_post_thumbnail('thumbnail', array('class' => 'd-block mx-auto')); ?>
                  </a>

                  <div class="d-flex flex-column gap-1 px-2 pt-2 w-100">
                    <!-- 詳細 -->
                    <?php if ($epFullAdrs) : ?>
                      <small class="fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= $epFullAdrs; ?>
                      </small>
                    <?php endif; ?>
                    <?php if ($epTel) : ?>
                      <small class="fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-phone-square"></i>
                        <span class="text-decoration-underline">
                          <a href="tel:<?= $epTel; ?>"><?= $epTel; ?></a>
                        </span>
                      </small>
                    <?php endif; ?>

                    <?php if ($epUrl) : ?>
                      <a href="<?= $epUrl; ?>" class="mt-auto btn btn-sm btn-outline-dark d-block" target="_blank">ウェブサイト</a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

              <!-- カテゴリ -->
              <?php if ($genre and count($genre) > 0) : ?>
                <div class="d-flex flex-wrap my-2">
                  <?php foreach ($genre as $i => $term) : ?>
                    <?php if ($i >= 10) break; ?>
                    <a href="/?s=&pt=<?= $post_type; ?>&g_ep[]=<?= $term->name; ?>" class="badge rounded-pill fw-normal text-decoration-none text-white bg-warning m-1 px-3 py-1" style="font-size:9pt;"><?= $term->name; ?></a>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php
        // ========================================================================
        // 求人検索
        // ------------------------------------------------------------------------
        elseif ($post_type == 'job_offers') :
          $cat_genres = array();
          $cat_genre  = array();

          $cat_job_type = get_term_without_parent($post_id, 'cat_job_type'); // 職種
          $cat_genres[] = get_term_without_parent($post_id, 'cat_job_offer_genre');                // 共通ジャンル
          $cat_genres[] = get_term_without_parent($post_id, 'cat_job_offer_genre_stay');           // 宿泊ジャンル
          $cat_genres[] = get_term_without_parent($post_id, 'cat_job_offer_genre_wedding');        // ウェディングジャンル
          $cat_genres[] = get_term_without_parent($post_id, 'cat_job_offer_genre_agriculture');    // 農業ジャンル

          if (is_array($cat_job_type) and count($cat_job_type)) $cat_job_type = array_values($cat_job_type);

          foreach ($cat_genres as $c_genre) {
            if (is_array($c_genre) and count($c_genre)) {
              foreach ($c_genre as $g) {
                $cat_genre[] = $g;
              }
            }
          }
          // ======================================================================== 
        ?>
          <div class="col-12 col-md-6 mb-3">
            <a href="<?= get_permalink(); ?>" class="text-decoration-none text-secondary">
              <div class="card rounded-3 p-2 d-flex flex-column gap-1" style="background-color:#f5f5ff;">
                <h2 class="m-0 h4" style=""><?= get_the_title(); ?></h2>
                <small class="text-secondary"><?= get_field('事業所名'); ?></small>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                  <small class="text-center" style="max-width:70px;font-size:10px;">
                    <?php
                    if (!empty($cat_job_type)) :
                      echo $cat_job_type[0]->name;
                    endif;
                    ?>
                  </small>

                  <?php if (intval(get_field('給与_自_'))) : ?>
                    <span class="h5 m-0">
                      <?= get_field('給与形態'); ?>
                      <span class="text-danger fw-bold">
                        <?php
                        if (get_field('給与_自_') > 9999) {
                          echo number_format(get_field('給与_自_') / 10000) . '</span>万円';
                        } else {
                          echo number_format(get_field('給与_自_')) . '</span>円';
                        }
                        echo '～';
                        ?>
                      </span>
                    <?php endif; ?>
                </div>

                <small class="d-flex flex-wrap gap-2 align-items-center">
                  <i class="fas fa-map-marker-alt"></i>
                  <span><?= get_field('就業場所') ? get_field('就業場所') : get_field('都道府県名') . get_field('住所'); ?></span>
                </small>

                <small class="d-flex flex-wrap gap-2 align-items-center">
                  <i class="fas fa-user-tie"></i>
                  <span><?= get_field('契約形態'); ?></span>
                </small>

                <small class="d-flex flex-wrap gap-2 align-items-center">
                  <i class="fas fa-clock"></i>
                  <div class="d-flex flex-column">
                    <span><?= date('G:i', strtotime(get_field('就労時間_自_'))) . '～' . date('G:i', strtotime(get_field('就労時間_至_'))); ?></span>

                    <?php if (get_field('就業時間タグ')) : ?>
                      <span><?= get_field('就業時間タグ'); ?></span>
                    <?php endif; ?>
                  </div>
                </small>

                <div class="d-flex flex-wrap gap-1">
                  <?php
                  if (!empty($cat_genre)) {
                    $priTags = [];
                    if (get_field('優先タグ')) {
                      $priTags = explode("\n", get_field('優先タグ'));
                      foreach ($priTags as $tag) {
                        // 0 : taxonomy
                        // 1 : term_id
                        $priTagTerm = explode('|', $tag);
                        $priTagTermIds[] = $priTagTerm[1];

                        $term = get_term_by('id', $priTagTerm[1], $priTagTerm[0]);
                        echo '<span class="badge bg-success">★ ' . $term->name . '</span>';
                      }
                    }

                    $cnt = 0;
                    foreach ($cat_genre as $i => $genre) {
                      if ($priTagTermIds && in_array($genre->term_id, $priTagTermIds)) {
                        continue;
                      }

                      if ($cnt < 5) {
                        echo '<span class="badge bg-info">' . $genre->name . '</span>';
                      } else {
                        break;
                      }
                      $cnt++;
                    }

                    if ((count($cat_genre) - count($priTags)) > 5) {
                      echo '<span class="badge bg-secondary">' . (count($cat_genre) - 5) . ' +</span>';
                    }
                  }
                  ?>
                </div>
              </div>
            </a>
          </div>
        <?php
        // ========================================================================
        // イベント検索
        // ------------------------------------------------------------------------
        elseif ($post_type == 'events') :
          $siteUrl    = get_field('特設サイト');
          $terms      = get_the_terms($post_id, 'cat_events');
          $thumnbnal  = get_field('ギャラリー') ? get_field('ギャラリー')[0]['sizes']['medium'] : null;
          // ======================================================================== 
        ?>
          <div class="col-12 col-md-6 mb-3">
            <a href="<?= get_permalink(); ?>" class="text-decoration-none text-secondary">
              <div class="card rounded-3 p-2 d-flex flex-column gap-1" style="background-color:#f5fffc;">
                <h2 class="m-0 h4">
                  <?= get_field('イベント中止') ? '【中止】' : ''; ?>
                  <?= get_the_title(); ?>
                </h2>

                <div class="d-flex w-100">
                  <img src="<?= get_field('ギャラリー')[0]['sizes']['thumbnail']; ?>">

                  <div class="d-flex flex-column gap-1 px-2" >
                    <?php
                      $startDate = (get_field('開催日_自_')) ? date('Y/n/j', strtotime(get_field('開催日_自_'))) : '';
                      $endDate   = (get_field('開催日_至_')) ? date('Y/n/j', strtotime(get_field('開催日_至_'))) : '';
                      if ($startDate or $endDate) {
                        $eventDate = ($startDate == $endDate) ? $startDate : $startDate . '～' . $endDate;
                      } else {
                        $eventDate = '無期限';
                      }

                      $startTime = (get_field('開催時間_自_')) ? date('G:i', strtotime(get_field('開催時間_自_'))) : '';
                      $endTime   = (get_field('開催時間_至_')) ? date('G:i', strtotime(get_field('開催時間_至_'))) : '';
                      $eventTime = ($startTime or $endTime) ? $startTime . '～' . $endTime : '';
                    ?>
                    <div class="d-flex flex-column gap-1">
                      <small class="fw-bold d-flex align-items-start gap-1">
                        <div class="text-center" style="min-width:20px;"><i class="fas fa-calendar-alt"></i></div>
                        <?= $eventDate; ?>
                      </small>

                      <?php if ($eventTime) : ?>
                        <small class="fw-bold d-flex align-items-start gap-1">
                          <div class="text-center" style="min-width:20px;"><i class="fas fa-clock"></i></div>
                          <?= $eventTime; ?>
                        </small>
                      <?php endif; ?>

                      <?php if (get_field('開催場所')) : ?>
                        <small class="fw-bold d-flex align-items-start gap-1">
                          <div class="text-center" style="min-width:20px;"><i class="fas fa-map-marker-alt"></i></div>
                          <?= get_field('開催場所'); ?>
                        </small>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                <?php if ($terms and count($terms) > 0) : ?>
                  <div class="d-flex flex-wrap mt-auto">
                    <?php foreach ($terms as $term) : ?>
                      <span class="badge rounded-pill fw-normal bg-warning m-1 px-3 py-1" style="font-size:9pt;"><?= $term->name; ?></span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>

              </div>
            </a>
          </div>
        <?php
        // ========================================================================
        // クーポン検索
        // ------------------------------------------------------------------------
        elseif ($post_type == 'coupons') :
          $terms = get_the_terms($post_id, 'cat_coupon');
          // ======================================================================== 
        ?>
        <div class="col-12 col-md-6 mb-3">
          <div data-bs-toggle="modal" data-bs-target="#coupon_preview_<?= $i; ?>" style="cursor:pointer">
            <div class="bg-white border border-2 border-success">
              <div class="d-flex">
                <div class="position-relative" style="width:40%;">
                  <?php if ($terms) : foreach ($terms as $term) : ?>
                      <small class="badge rounded-pill bg-warning position-absolute m-1 px-3 py-1 top-0 start-0"><?= $term->name; ?></small>
                  <?php endforeach;
                  endif; ?>

                  <?= the_post_thumbnail('thumbnail', array('class' => 'd-block w-100')); ?>
                </div>

                <div class="d-flex flex-column py-2" style="width:60%">
                  <div class="px-2">
                    <span class="h4 d-block"><?= the_title(); ?></span>
                    <span><?= get_field('条件'); ?></span>
                  </div>
                  <div class="mt-auto d-flex align-items-center justify-content-center flex-wrap gap-1 px-2">
                    <?php if (get_field('定価')) : ?>
                      <small style="text-decoration: line-through;"><?= number_format(get_field('定価')); ?>円</small>
                      <small><i class="bi bi-caret-right-fill"></i></small>
                    <?php endif; ?>

                    <?php if (get_field('割引種別') == '円引' and get_field('割引値') < get_field('定価')) : ?>
                      <span class="text-danger fw-bold coupon-price outlined h4 m-0">￥<?= number_format(get_field('定価') - get_field('割引値')); ?></span>
                    <?php elseif (get_field('割引種別') == '円引' and get_field('割引値') == get_field('定価')) : ?>
                      <span class="text-danger fw-bold coupon-price outlined h4 m-0">無料！</span>
                    <?php elseif (get_field('割引種別') == '％引' and get_field('割引値') == 100) : ?>
                      <span class="text-danger fw-bold coupon-price outlined h4 m-0">無料！</span>
                    <?php elseif (get_field('割引種別') == '％引' and get_field('割引値')) : ?>
                      <span class="text-danger fw-bold coupon-price outlined h4 m-0"><?= get_field('割引値'); ?>％OFF</span>
                    <?php else : //noop ?>
                    <?php endif; ?>
                  </div>
                </div>
              </div>

            </div>
            <div class="bg-success text-center text-white py-1">クーポンのご利用はこちら</div>
          </div>

          <div class="modal fade" id="coupon_preview_<?= $i; ?>" tabindex="-1" aria-labelledby="coupon_preview_<?= $i; ?>_label" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen-sm-down m-0 mx-auto w-100 h-100">
              <div class="modal-content h-100 top-50 start-50 translate-middle">
                <div class="modal-header bg-primary align-items-center">
                  <h5 class="modal-title text-white text-center d-block w-100" id="coupon_preview_<?= $i; ?>_label">クーポン</h5>
                  <i class="fas fa-times text-white p-1" style="cursor:pointer;" data-bs-dismiss="modal" aria-label="Close"></i>
                  <!-- <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button> -->
                </div>
                <div class="modal-body p-0 overflow-hidden" style="flex:none;">
                  <?= the_post_thumbnail('full', array('class' => 'd-block w-100')); ?>
                </div>
                <div class="modal-footer flex-column">
                  <span class="d-block w-100 h2 text-center px-2 my-2" style="color:#555;"><?= the_title(); ?></span>
                  <div class="d-flex align-items-end justify-content-end px-2">
                    <?php if (get_field('定価')) : ?>
                      <del style="color:red;"><span class="px-2 coupon-price" style="color:#555;"><?= number_format(get_field('定価')); ?>円</span></del>
                      <i class="bi bi-caret-right-fill mx-2" style="color:#555;"></i>
                    <?php endif; ?>
                    <div class="">
                      <?php if (get_field('割引種別') == '円引' and get_field('割引値') < get_field('定価')) : ?>
                        <span class="text-danger fw-bold coupon-price h1 m-0" style="font-size:250;">￥<?= number_format(get_field('定価') - get_field('割引値')); ?></span>
                      <?php elseif (get_field('割引種別') == '円引' and get_field('割引値') == get_field('定価')) : ?>
                        <span class="text-danger fw-bold coupon-price h1 m-0" style="font-size:250;">無料！</span>
                      <?php elseif (get_field('割引種別') == '％引' and get_field('割引値') == 100) : ?>
                        <span class="text-danger fw-bold coupon-price h1 m-0" style="font-size:250;">無料！</span>
                      <?php elseif (get_field('割引種別') == '％引' and get_field('割引値')) : ?>
                        <span class="text-danger fw-bold coupon-price h1 m-0" style="font-size:250;"><?= get_field('割引値'); ?>％OFF</span>
                      <?php else : // noop ?>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
                <div class="modal-footer flex-column">
                  <div class="text-center fw-bold w-100"><?= (get_field('利用終了日')) ? date('Y年n月j日', strtotime(get_field('利用終了日'))) . 'まで' : '無期限'; ?></div>

                  <?php if (get_field('条件詳細')) : ?>
                    <small><?= str_replace(["\r", "\n", "\r\n"], "<br>", get_field('条件詳細')); ?></small>
                  <?php endif; ?>
                  <div class="text-center"><a href="https://www.google.com/maps/search/?api=1&query=<?= get_field('都道府県名') . get_field('住所'); ?>" target="_blank"><i class="bi bi-geo-alt-fill me-2"></i>お店の位置を確認</a></div>
                </div>
              </div>
            </div>
          </div>

          <div class="modal fade" id="coupon_detail_<?= $i; ?>" tabindex="-1" aria-labelledby="coupon_detail_<?= $i; ?>_label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <?= str_replace(["\r", "\n", "\r\n"], '<br>', get_field('利用終了日')); ?>
            </div>
            <div class="modal-footer flex-column justify-content-center">
              <button type="button" class="btn btn-secondary"></button>
            </div>
          </div>
        </div>

        <?php
          // ========================================================================
          // 子育て検索、料理・レシピ検索
          // ------------------------------------------------------------------------
          else:

          // print_r($post);
          // $genre = get_the_terms($post_id, 'cat_recipes');
          // print_r($genre);
          $permaUrl   = ($epOutLink and $epUrl) ? $epUrl : $permaUrl;
          $tags = [];
          if (is_array($post_type)) {
            foreach($post_type as $pt) {
              if (taxonomy_exists("cat_{$pt}")) {
                $_tags = get_the_terms($post->ID, "cat_{$pt}");
                $tags = array_merge($tags, $_tags);
              }
            }
          } else {
            $tags = get_the_terms($post->ID, "cat_{$post_type}");
          }
          // print_r($tags);
          // ========================================================================
        ?>
        <div class="col-12 col-sm-6 mb-3">
          <div class="d-flex justify-content-between align-items-center bg-primary p-2 gap-1">
            <a href="<?= $permaUrl; ?>" class="fw-bold text-white"><?= the_title(); ?></a>
            <div></div>
            <div class="d-flex flex-wrap">
              <?php foreach(get_the_terms($post->ID, "category") as $cat): ?>
                <span class="badge rounded-pill fw-normal bg-success m-1 px-3 py-1" style="font-size:9pt;"><?= $cat->name; ?></span>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="d-sm-flex bg-white mb-3 w-100 border border-primary border-top-0">
            <?php if($thumnbnal): ?>
            <div class="mx-auto" style="width:100%;max-height:180px;">
              <a href="<?= $permaUrl; ?>">
                <img src="<?= $thumnbnal; ?>" class="d-block w-100" style="height:180px;object-fit:cover;" alt="">
              </a>
            </div>
            <?php endif; ?>

            <div class="d-flex flex-column justify-content-between gap-1 p-2" style="<?= ($thumnbnal) ? 'min-width:calc(100% - 200px);' : 'width:100%;' ?>">
              <?php
                $content = wp_strip_all_tags( get_the_content() );
                $content = strip_shortcodes( $content );
                if (mb_strlen($content)>50) {
                  $content = wp_trim_words($content, 50, '…' );
                }
              ?>
              <span><?= $content ?></span>

              <!-- タグ一覧 -->
              <?php if($tags and count($tags) > 0) : ?>
                <div class="d-flex flex-wrap">
                  <?php foreach($tags as $tag): ?>
                    <span class="badge rounded-pill fw-normal bg-warning m-1 px-3 py-1" style="font-size:9pt;"><?= $tag->name; ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <!-- 投稿者 -->
              <div>
                <small class="fw-bold d-flex align-items-center gap-2">
                  <span style="width:5rem;"><i class="fas fa-edit">投稿者</i></span>
                  <?= the_author_meta('display_name'); ?>
                </small>
                <small class="fw-bold d-flex align-items-center gap-2">
                  <span style="width:5rem;"><i class="fas fa-clock">投稿日時</i></span>
                  <?= get_the_date('Y年m月d日  H:i'); ?>
                </small>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>

      <?php endwhile; else : ?>

      <div class="col card w-100 align-items-center px-2 mx-2 mb-4" style="height:100px;">
        <p class="my-auto fw-bold">お探しの<?= $post_type_name; ?>は見つかりませんでした。</p>
      </div>

    <?php endif; wp_reset_postdata(); ?>

  </div>
  <?php get_template_part('template-parts/custom_pagination', null, $the_query); ?>
</div>
