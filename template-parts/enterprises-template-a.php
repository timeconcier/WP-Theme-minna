<?php
  $epId       = get_the_author_meta('ID');
  $epName     = get_the_author_meta('事業所名');
  $epUrl      = get_the_author_meta('サイトurl');
  $epTel      = get_the_author_meta('電話番号');
  $epZipCode  = get_the_author_meta('郵便番号');
  $epFullAdrs = get_the_author_meta('都道府県名') . get_the_author_meta('住所1_市名_') . get_the_author_meta('住所2');
  $epLat      = get_the_author_meta('緯度');
  $epLng      = get_the_author_meta('経度');
  $thumbnail  = get_the_post_thumbnail_url();
  $noImg_url  = wp_get_attachment_url(NO_IMAGE_ID);

  $pageColor  = colorType($post->ID);
?>



<div class="position-relative">
  <div class="d-flex align-items-center bg-info px-3 py-2 w-100 shadow-sm" style="width:calc(100% - 20px);top:10px;">
    <h2 class="text-white m-0 h4 ps-3"><?= $epName; ?></h2>
    <div class="flex-grow-1"></div>

    <?php if ($epTel) : ?>
      <a href="tel:<?= $epTel; ?>" class="d-block d-flex align-items-center m-0 h4 text-decoration-none text-end">
        <span class="d-sm-inline-block d-none text-white" style="white-space:nowrap;"><?= $epTel; ?></span>

        <i class="bi bi-telephone-fill d-sm-none d-block fs-4 ms-2 px-3 btn btn-secondary text-info"></i>
      </a>
    <?php endif; ?>
  </div>
</div>

<div class="bg-secondary w-100 mt-2 px-2">
  <div class="container">
    <div class="row flex-column flex-sm-row mx-auto align-items-end" style="max-width:800px;">
      <?php if (has_post_thumbnail()) : ?>
      <div class="col">
        <img src="<?= $thumbnail; ?>" alt="<?= get_the_title(); ?>" class="d-block align-self-start w-sm-50 w-100 mx-auto">
      </div>
      <?php endif; ?>

      <div class="col">
        <div class="d-flex flex-column align-self-stretch mx-auto mt-2" style="min-width:300px; max-width:50%;">
        <div class="d-flex flex-column p-2 card mt-auto">
          <span class="d-flex gap-2 align-items-center">
            <i class="fas fa-tenge d-block text-center" style="width:25px;"></i>
            <?= zipCodeFormat($epZipCode); ?>
          </span>

          <?php if ($epFullAdrs) : ?>
            <span class="d-flex gap-2 align-items-center">
              <i class="fas fa-map-marker-alt d-block text-center" style="width:25px;"></i>
              <?= $epFullAdrs; ?>
            </span>
          <?php endif; ?>

          <?php if ($epTel) : ?>
            <span class="d-flex gap-2 align-items-center">
              <i class="fas fa-phone d-block text-center" style="width:25px;"></i>
              <?= $epTel; ?>
            </span>
          <?php endif; ?>
        </div>
        <div>
          <?php if ($epUrl) : ?>
            <a href="<?php echo $epUrl; ?>" class="d-block btn btn-warning">ウェブサイト</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>


  <div class="py-4 w-100 mx-auto" style="max-width:800px;">
    <?php the_field('概要'); ?>
  </div>

  <?php if (get_field('クーポン有無') and get_field('クーポン')) : ?>
    <div class="p-4 mb-4">
      <div class="w-100 mx-auto" style="max-width:800px;">
        <div class="d-flex mb-3">
          <hr class="my-3 ms-0 me-3 border-0 bg-dark flex-grow-1" style="height:2px;">
          <h3>クーポン情報</h3>
          <hr class="my-3 ms-3 me-0 border-0 bg-dark flex-grow-1" style="height:2px;">
        </div>
        <div class="edit-area">
          <!-- // ここにクーポン情報が入ります（ループ） -->
          <?= get_field('クーポン'); ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <?php
  $posts      = array();
  $postTypes  = array('events', 'coupons', 'job_offers');
  // イベント情報
  foreach ($postTypes as $pt) {
    $args = array(
      'author'         => $epId,
      'post_type'      => $pt,
      'posts_per_page' => -1,
    );

    $posts[$pt] = get_posts($args);
  }
  ?>

  <?php foreach ($posts as $pt => $pt_post) :
    switch ($pt) {
      case 'events':
        $pt_name = 'イベント';
        break;
      case 'coupons':
        $pt_name = 'クーポン';
        break;
      case 'job_offers':
        $pt_name = '求人';
        break;
    }
  ?>

    <div class="py-4 mb-4">
      <div class="w-100 mx-auto" style="max-width:800px;">
        <div class="d-flex mb-3">
          <hr class="my-3 ms-0 me-3 border-0 bg-dark flex-grow-1" style="height:2px;">
          <h3><?= $pt_name; ?>情報</h3>
          <hr class="my-3 ms-3 me-0 border-0 bg-dark flex-grow-1" style="height:2px;">
        </div>
        <div class="edit-area d-flex" style="overflow-x: auto;">
          <?php
          if (count($pt_post) > 0) :
            foreach ($pt_post as $post) :
              setup_postdata($post);
              $i++;
              $post_id   = get_the_ID();
              $thumnbnal = get_the_post_thumbnail_url($post_id);
              $thumnbnal = ($thumnbnal) ? $thumnbnal : wp_get_attachment_url(NO_IMAGE_ID);
          ?>

              <?php // イベント =======================================================================================
              if ($pt == 'events') : ?>
                <div class="card card-loop d-flex" style="min-width:187px;">
                  <?php if (get_field('ギャラリー')) : ?>
                    <img src="<?= get_field('ギャラリー')[0]['url']; ?>" class="card-img-top" style="max-height:220px;max-width:220px;" alt="<?= the_title(); ?>">
                  <?php else : ?>
                    <img src="<?= $noImg_url; ?>" class="card-img-top" style="" alt="<?= the_title(); ?>">
                  <?php endif; ?>
                  <div class="card-body d-flex flex-column gap-2">
                    <h5 class="card-title m-0 mt-auto"><?= the_title(); ?></h5>
                    <a class="btn btn-primary d-block text-center" href="<?= get_permalink(); ?>">詳しく見る</a>
                  </div>
                </div>
              <?php
              // ========================================================================
              // クーポン
              // ------------------------------------------------------------------------
              elseif ($pt == 'coupons') :
                $terms = get_the_terms($post_id, 'cat_coupon');
                // ========================================================================  
              ?>
                <div class="p-1 col-sm-6 col-12" data-bs-toggle="modal" data-bs-target="#coupon_preview_<?= $i; ?>" style="cursor:pointer">
                  <div class="bg-white border border-2 border-success">
                    <div class="d-flex">
                      <div class="position-relative" style="width:40%;">
                        <?php if ($terms) : foreach ($terms as $term) : ?>
                            <small class="badge rounded-pill bg-warning position-absolute m-1 px-3 py-1 top-0 start-0"><?= $term->name; ?></small>
                        <?php endforeach;
                        endif; ?>
                        <img src="<?= $thumnbnal; ?>" class="d-block w-100" alt="">
                      </div>

                      <div class="d-flex flex-column py-2" style="width:60%">
                        <div class="px-2">
                          <span class="h4 d-block"><?= the_title(); ?></span>
                          <span><?= get_field('条件'); ?></span>
                        </div>
                        <div class="mt-auto d-flex align-items-center justify-content-center flex-wrap gap-1 px-2">
                          <small style="text-decoration: line-through;"><?= number_format(get_field('定価')); ?>円</small>
                          <small><i class="bi bi-caret-right-fill"></i></small>


                          <?php if (get_field('割引種別') == '円引' and get_field('割引値') < get_field('定価')) : ?>
                            <span class="text-danger fw-bold coupon-price outlined h4 m-0">￥<?= number_format(get_field('定価') - get_field('割引値')); ?></span>
                          <?php elseif (get_field('割引種別') == '円引' and get_field('割引値') == get_field('定価')) : ?>
                            <span class="text-danger fw-bold coupon-price outlined h4 m-0">無料！</span>
                          <?php elseif (get_field('割引種別') == '％引' and get_field('割引値') == 100) : ?>
                            <span class="text-danger fw-bold coupon-price outlined h4 m-0">無料！</span>
                          <?php else : ?>
                            <span class="text-danger fw-bold coupon-price outlined h4 m-0"><?= get_field('割引値'); ?>％OFF</span>
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
                      <div class="modal-body p-0 overflow-hidden" style="background-image:url('<?= $thumnbnal; ?>');background-size:cover;background-position:top 50% right 50%;"></div>
                      <div class="modal-footer flex-column">
                        <span class="d-block w-100 h1 text-center px-2 my-2" style="color:#555;"><?= the_title(); ?></span>
                        <div class="d-flex align-items-end justify-content-end px-2">
                          <del style="color:red;"><span class="px-2 coupon-price" style="color:#555;"><?= number_format(get_field('定価')); ?>円</span></del>
                          <i class="bi bi-caret-right-fill mx-2" style="color:#555;"></i>
                          <div class="">
                            <?php if (get_field('割引種別') == '円引' and get_field('割引値') < get_field('定価')) : ?>
                              <span class="text-danger fw-bold coupon-price h1 m-0" style="font-size:250;">￥<?= number_format(get_field('定価') - get_field('割引値')); ?></span>
                            <?php elseif (get_field('割引種別') == '円引' and get_field('割引値') == get_field('定価')) : ?>
                              <span class="text-danger fw-bold coupon-price h1 m-0" style="font-size:250;">無料！</span>
                            <?php elseif (get_field('割引種別') == '％引' and get_field('割引値') == 100) : ?>
                              <span class="text-danger fw-bold coupon-price h1 m-0" style="font-size:250;">無料！</span>
                            <?php else : ?>
                              <span class="text-danger fw-bold coupon-price h1 m-0" style="font-size:250;"><?= get_field('割引値'); ?>％OFF</span>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer flex-column">
                        <div class="text-center fw-bold w-100"><?= (get_field('利用終了日')) ? date('Y年n月j日', strtotime(get_field('利用終了日'))) . 'まで' : '無期限'; ?></div>
                        <span>条件</span>
                        <div class="d-flex justify-content-center w-100">
                          <div class="coupon_detail_text d-none"></div>
                          <?php if (get_field('条件詳細')) : ?>
                            <div class="w-50 text-center"><a href="#" data-tippy-content="<?= str_replace(["\r", "\n", "\r\n"], "<br>", get_field('条件詳細')); ?>"><i class="bi bi-info-circle-fill me-2"></i>使用条件詳細</a></div>
                          <?php endif; ?>
                          <div class="w-50 text-center"><a href="https://www.google.com/maps/search/?api=1&query=<?= get_field('都道府県名') . get_field('住所'); ?>" target="_blank"><i class="bi bi-geo-alt-fill me-2"></i>お店の位置を確認</a></div>
                        </div>
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
              <?php // 求人 =======================================================================================
              elseif ($pt == 'job_offers') :
                $job_type = get_the_terms($post_id, 'cat_job_type');
                $terms = get_the_terms($post_id, 'cat_job_offer_genre');
              ?>
                <div class="card card-loop" style="width:250px; min-width:250px;">

                  <div class="card-body d-flex flex-column gap-2 p-2">
                    <div><span class="badge badge-sm rounded-pill bg-success"><?= get_field('契約形態'); ?></span></div>
                    <h5 class="card-title m-0"><?= the_title(); ?></h5>
                    <div>
                      <span><?= get_field('給与形態'); ?>：</span>
                      <strong class="text-danger">
                        <?= number_format(get_field('給与_自_')); ?>
                        <?= (get_field('給与_至_')) ? '円～' : '円'; ?>
                      </strong>
                    </div>
                    <?php if (!empty($job_type)) : ?>
                      <small>
                        <?php if ($job_type[0]) : ?><?= $job_type[0]->name; ?><?php endif; ?><br>
                        <?php if ($job_type[1]) : ?>┗ <?= $job_type[1]->name; ?><?php endif; ?>
                      </small>
                    <?php endif; ?>
                    <a class="btn btn-primary d-block text-center mt-auto" href="<?= get_permalink(); ?>">詳しく見る</a>
                    <small class="d-block text-end">掲載期限：<?= (get_field('掲載期限')) ? date('Y/m/d', strtotime(get_field('掲載期限'))) : '無期限'; ?></small>
                  </div>
                </div>
              <?php endif; ?>

            <?php
            endforeach;
            wp_reset_postdata();
          else :
            ?>
            <div class="text-center">
              <?php
              switch ($pt) {
                case 'events':      // イベント
                  echo '近日実施される';
                  break;
                case 'coupons':     // クーポン
                  echo '利用可能な';
                  break;
                case 'job_offers':   // 求人
                  echo '掲載中の';
                  break;
              }
              echo "{$pt_name}はありません。"
              ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

  <?php endforeach; ?>
  <?php // endif;
  ?>

  <?php   // イベント情報
  $jo_args = array(
    'post_type'      => 'job_offers',
    'posts_per_page' => -1,
    'meta_query' => [
      [
        'key'     => '事業所id',
        'value'   => $epId,
      ],
    ]
  );
  $jo_posts = get_posts($jo_args);
  if (count($jo_posts) > 0) :
  ?>
    <div class="p-4">
      <div class="w-100 mx-auto" style="max-width:800px;">
        <div class="d-flex mb-3">
          <hr class="my-3 ms-0 me-3 border-0 bg-dark flex-grow-1" style="height:2px;">
          <h3>求人情報</h3>
          <hr class="my-3 ms-3 me-0 border-0 bg-dark flex-grow-1" style="height:2px;">
        </div>
        <div class="edit-area d-flex" style="overflow-x: auto;">
          <?php foreach ($jo_posts as $post) : setup_postdata($post); ?>
            <?php $cat_ocp = get_the_terms(get_the_ID(), 'cat_occupation'); ?>

            <div class="card card-loop" style="min-width:187px;">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title m-0"><?= the_title(); ?></h5>
                <div class="card-text list-group list-group-flush">
                  <div class="list-group-item px-0">
                    <small class="d-block">
                      <?php foreach ($cat_ocp as $ocp) : ?>
                        <span class="badge bg-warning py-1 me-2"><?= $ocp->name; ?></span>
                      <?php endforeach; ?>
                    </small>
                  </div>
                  <div class="list-group-item px-0">
                    <small class="d-block fw-bold"><?= get_field('雇用形態'); ?></small>
                    <small class="d-block"><span class="badge bg-success py-1 me-2"><?= get_field('給与形態'); ?>制</span><?= number_format(get_field('給与_自_')); ?>円 ～</small>
                  </div>
                </div>
                <a class="btn btn-primary text-center mt-auto" href="<?= home_url("job_offers/{$post->post_name}"); ?>">詳しく見る</a>
              </div>
            </div>

          <?php endforeach;
          wp_reset_postdata(); ?>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php if ($epLat and $epLng) : ?>
  <iframe src="https://www.google.com/maps?output=embed&q=<?= $epLat . ', ' . $epLng; ?>&&t=m&z=16" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
<?php else : ?>
  <iframe src="https://www.google.com/maps?output=embed&q=<?= $epFullAdrs; ?>&z=16" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
<?php endif; ?>


<div class="px-2">
  <?php // 検索キーワード一覧
  $keywords = get_field('検索キーワード');
  if ($keywords) :
    $arrKwds = explode(',', $keywords);

    foreach ($arrKwds as $kwd) :
  ?>
    <a href="<?= home_url("/?s={$kwd}&pt=enterprises"); ?>" title="検索：<?= $kwd; ?>">#<?= $kwd; ?></a>
  <?php endforeach; endif; ?>
</div>













<?php
if ($post->ID == 1895) {
  // $posts = get_posts(array(
  //     'post_type' => 'enterprises',
  //     'posts_per_page' => -1,
  // ));

  // $postAcfFields = acf_get_fields('group_6139c86bd9b08');
  // $userAcfFields = acf_get_fields('group_614967bbadd6c');
  // $allAcfFields = array_merge($postAcfFields, $userAcfFields);
  // // print_r($allAcfFields);
  // foreach($posts as $p) {
  //     $rate = 0;
  //     $post_id = $p->ID;

  //     // タクソノミー情報
  //     foreach(get_post_taxonomies($post_id) as $tax) {
  //         if($tax !== 'category' and $tax !== 'post_tag'){
  //             $terms = wp_get_post_terms( $post_id, $tax, ['fields' => 'ids'] );
  //             if($terms) $rate += 10;  // 1カテゴリーにつき10点(max 20pt)
  //         }
  //     }
  //     echo $post_id." - ";

  //     // サムネイル情報
  //     $thumbnail_id = get_post_thumbnail_id( $post_id );
  //     if($thumbnail_id) $rate += 20;  // 画像登録で20点

  //     $postAcf = get_fields($post_id);
  //     $userAcf = get_fields("user_{$p->post_author}");
  //     $acfFields = array_merge($postAcf, $userAcf);

  //     /** *************************************
  //      * ********** 除外フィールド ************
  //      * *************************************/
  //     $excludeFlds = [
  //         'ページテンプレート',   // 事業者投稿
  //         '事業所id',             // 事業者投稿
  //         '入力達成率',           // 事業者投稿
  //         '外部リンクを使用',     // 基本情報
  //         '求人規約承諾',         // 基本情報
  //         '緯度',                 // 基本情報
  //         '経度',                 // 基本情報
  //     ];
  //     /** ********************************** */

  //     $acfCount = count($allAcfFields) - count($excludeFlds);
  //     $acfPoint = round(60 / $acfCount, 1);

  //     foreach($allAcfFields as $arr) {
  //         if( !in_array($arr['name'], $excludeFlds) ) {
  //             if($acfFields[$arr['name']]) $rate += $acfPoint;
  //         }
  //     }

  //     $rate = ($rate > 100) ? 100 : round($rate);

  //     echo $rate."\n";
  //     update_field('入力達成率', $rate, $post_id);
  // }

  // print_r($posts);
}
