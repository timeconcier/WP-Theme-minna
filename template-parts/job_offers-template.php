
<?php
  $thumbnail  = (has_post_thumbnail()) ? get_the_post_thumbnail_url() : wp_get_attachment_url(NO_IMAGE_ID);
  $epId       = get_the_author_meta('ID');

  // 事業所詳細 投稿情報取得
  $posts_array = get_posts(array(
    'posts_per_page' => 1,
    'post_type'      => 'enterprises',
    'meta_key'       => '事業所id',
    'meta_value'     => $epId,
  ));
  $epPostId    = $posts_array[0]->ID;
  $epName      = get_the_author_meta('事業所名');
  $epUrl       = get_the_author_meta('サイトurl');
  $epPermalink = get_post_permalink($epPostId);
  $outsideLink = get_the_author_meta('外部リンクを使用');
  $title       = is_single() ? get_the_title() : get_the_title($_GET['id']);
  $joPostId    = is_single() ? get_the_ID() : $_GET['id'];

  $cat_genres[] = get_term_without_parent($post_id, 'cat_job_offer_genre');                // 共通ジャンル
  $cat_genres[] = get_term_without_parent($post_id, 'cat_job_offer_genre_stay');           // 宿泊ジャンル
  $cat_genres[] = get_term_without_parent($post_id, 'cat_job_offer_genre_wedding');        // ウェディングジャンル
  $cat_genres[] = get_term_without_parent($post_id, 'cat_job_offer_genre_agriculture');    // 農業ジャンル

  foreach ($cat_genres as $c_genre) {
    if (is_array($c_genre) and count($c_genre)) {
      foreach ($c_genre as $g) {
        $cat_genre[] = $g;
      }
    }
  }

  $acfFields = get_fields($joPostId);
?>


  <article class="mb-5 px-2">
    <div class="card rounded-3 p-2 d-flex flex-column gap-1">
      <h2 class="m-0 h4" style=""><?= $title; ?></h2>

      <div class="d-flex flex-wrap gap-2 align-items-center">
        <small class="text-center" style="max-width:70px;font-size:10px;">
          <?php
          if (!empty($cat_job_type)) :
            if (count($cat_job_type) == 1) :
              echo $cat_job_type[0]->name;
            elseif (count($cat_job_type) == 2) :
              echo $cat_job_type[1]->name;
            endif;
          endif;
          ?>
        </small>

        <span class="h5 m-0">
          <?= get_field('給与形態', $joPostId); ?>
          <span class="text-danger fw-bold">
            <?php
            if (get_field('給与_自_', $joPostId)) {
              if (get_field('給与_自_', $joPostId) > 9999) {
                echo number_format(get_field('給与_自_', $joPostId) / 10000) . '</span>万円';
              } else {
                echo number_format(get_field('給与_自_', $joPostId)) . '</span>円';
              }
              echo '～';
            }
            ?>
          </span>
      </div>

      <small class="d-flex flex-wrap gap-2 align-items-center">
        <i class="fas fa-map-marker-alt"></i>
        <span><?= get_field('都道府県名', $joPostId) . get_field('住所', $joPostId); ?></span>
      </small>

      <small class="d-flex flex-wrap gap-2 align-items-center">
        <i class="fas fa-user-tie"></i>
        <span><?= get_field('契約形態', $joPostId); ?></span>
      </small>

      <?php if (get_field('就労時間_自_', $joPostId) and get_field('就労時間_至_', $joPostId)) : ?>
        <small class="d-flex flex-wrap gap-2 align-items-center">
          <i class="fas fa-clock"></i>
          <div class="d-flex flex-column">
            <span><?= date('G:i', strtotime(get_field('就労時間_自_', $joPostId))) . '～' . date('G:i', strtotime(get_field('就労時間_至_', $joPostId))); ?></span>

            <?php if (get_field('就業時間タグ', $joPostId)) : ?>
              <span><?= get_field('就業時間タグ', $joPostId); ?></span>
            <?php endif; ?>
          </div>
        </small>
      <?php endif; ?>


      <?php if (!empty($cat_genre)) : ?>
        <div class="d-flex flex-wrap gap-1">
          <?php
          $priTags = [];
          if (get_field('優先タグ', $joPostId)) {
            $priTags = explode("\n", get_field('優先タグ', $joPostId));
            foreach ($priTags as $tag) {
              // 0 : taxonomy
              // 1 : term_id
              $priTagTerm = explode('|', $tag);
              $priTagTermIds[] = $priTagTerm[1];
              switch ($term->taxonomy) {
                case 'cat_job_offer_genre_agriculture':
                  $taxQuery = 'g_jo_agr';
                  break;
                case 'cat_job_offer_genre_wedding':
                  $taxQuery = 'g_jo_wed';
                  break;
                case 'cat_job_offer_genre_stay':
                  $taxQuery = 'g_jo_sty';
                  break;
                default:
                  $taxQuery = 'g_jo';
                  break;
              }

              $term = get_term_by('id', $priTagTerm[1], $priTagTerm[0]);
              echo '<a href="/?s=&pt=job_offers&' . $taxQuery . '=' . $term->name . '" class="text-decolation-none"><span class="badge bg-success">★ ' . $term->name . '</a></span>';
            }
          }

          $cnt = 0;
          foreach ($cat_genre as $i => $genre) {
            if ($priTagTermIds && in_array($genre->term_id, $priTagTermIds)) {
              continue;
            } else {
              switch ($genre->taxonomy) {
                case 'cat_job_offer_genre_agriculture':
                  $taxQuery = 'g_jo_agr';
                  break;
                case 'cat_job_offer_genre_wedding':
                  $taxQuery = 'g_jo_wed';
                  break;
                case 'cat_job_offer_genre_stay':
                  $taxQuery = 'g_jo_sty';
                  break;
                default:
                  $taxQuery = 'g_jo';
                  break;
              }
              echo '<a href="/?s=&pt=job_offers&' . $taxQuery . '=' . $genre->name . '" class="text-decolation-none"><span class="badge bg-info">' . $genre->name . '</a></span>';
            }
          }
          ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="bg-dark text-white d-flex flex-column py-2 px-3 my-3">
      <small class="text-end"><?= get_field('事業所名', $joPostId); ?></small>
    </div>

    <?php
    // シェアボタン
    get_template_part('template-parts/share-buttons'); ?>

    <div class="mb-5">
      <div class="w-100 mx-auto">

        <h3 class="d-block border-bottom border-3 border-primary">募集内容</h3>

        <div class="mb-3">
          <?php if (get_field('就業場所', $joPostId)) : ?>
            <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
              <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">就業場所</div>
              <div class="p-2"><?= get_field('就業場所', $joPostId); ?></div>
            </div>
          <?php endif; ?>

          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">雇用形態</div>
            <div class="p-2">
              <div><?= get_field('契約形態', $joPostId); ?></div>

              <?php if (get_field('雇用_補足', $joPostId)) : ?>
                <div class="mt-3 mb-1">
                  <small class="d-block fw-bold">【雇用形態補足】</small>
                  <?= str_replace("\n", '<br>', get_field('雇用_補足', $joPostId)); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <?php if (get_field('雇用開始日', $joPostId)) : ?>
            <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
              <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;"><?= (in_array(get_field('契約形態', $joPostId), ['請負', '業務委託'], true)) ? '契約' : '雇用'; ?>期間</div>
              <div class="p-2">
                <?= date('Y年n月j日', strtotime(get_field('雇用開始日', $joPostId))); ?> ～
                <?php if (get_field('雇用終了日', $joPostId)) date('Y年m月d日', strtotime(get_field('雇用終了日', $joPostId))); ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">募集人数</div>
            <div class="p-2"><?= get_field('募集人数', $joPostId) ? get_field('募集人数', $joPostId) . ' 名' : '無制限'; ?></div>
          </div>
        </div>


        <h4>待遇</h4>

        <div class="mb-3">
          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">給与について</div>
            <div class="p-2">
              <div>
                <?= get_field('給与形態', $joPostId); ?>
                <?php
                if (get_field('給与_自_', $joPostId)) {
                  echo (get_field('給与_自_', $joPostId) >= 10000) ? number_format(get_field('給与_自_', $joPostId) / 10000) . '万円' : number_format(get_field('給与_自_', $joPostId)) . '円';
                  echo ' ～ ';
                }
                if ((get_field('給与_至_', $joPostId))) {
                  if (!get_field('給与_自_', $joPostId)) echo ' ～ ';
                  echo (get_field('給与_至_', $joPostId) >= 10000) ? number_format(get_field('給与_至_', $joPostId) / 10000) . '万円' : number_format(get_field('給与_至_', $joPostId)) . '円';
                }
                ?>
              </div>

              <?php if (get_field('給与_補足', $joPostId)) : ?>
                <div class="mt-3 mb-1">
                  <small class="d-block fw-bold">【給与形態補足】</small>
                  <?= str_replace("\n", '<br>', get_field('給与_補足', $joPostId)); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <?php if (get_field('福利厚生', $joPostId)) : ?>
            <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
              <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">福利厚生</div>
              <div class="p-2">
                <?= str_replace("\n", '<br>', get_field('福利厚生', $joPostId)); ?>
              </div>
            </div>
          <?php endif; ?>
        </div>



        <h3 class="d-block border-bottom border-3 border-primary">仕事内容</h3>

        <h4>勤務条件</h4>

        <div class="mb-3">
          <?php if (get_field('就労時間_自_', $joPostId) or get_field('就労時間_至_', $joPostId)) : ?>
            <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
              <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">勤務時間</div>
              <div class="p-2">
                <div>
                  <?= date('G：i', strtotime(get_field('就労時間_自_', $joPostId))); ?> ～ <?= date('G：i', strtotime(get_field('就労時間_至_', $joPostId))); ?>
                </div>

                <?php if (get_field('勤務時間_補足', $joPostId)) : ?>
                  <div class="mt-3 mb-1">
                    <small class="d-block fw-bold">【勤務時間補足】</small>
                    <?= str_replace("\n", '<br>', get_field('勤務時間_補足', $joPostId)); ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">時間外労働</div>
            <div class="p-2">
              <div><?= (get_field('時間外労働の有無', $joPostId)) ? 'あり' : 'なし'; ?></div>

              <?php if (get_field('時間外労働に関する補足説明', $joPostId)) : ?>
                <div class="mt-3 mb-1">
                  <small class="d-block fw-bold">【勤務日補足】</small>
                  <?= str_replace("\n", '<br>', get_field('時間外労働に関する補足説明', $joPostId)); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">勤務曜日</div>
            <div class="p-2">
              <?php
              $array_weeks = ['月', '火', '水', '木', '金', '土', '日'];
              $selected_weeks = get_field('勤務曜日', $joPostId);
              usort($selected_weeks, function ($a, $b) use ($array_weeks) {
                return array_search($a, $array_weeks) <=> array_search($b, $array_weeks);
              });
              ?>
              <div><?= implode('、', $selected_weeks); ?></div>

              <?php if (get_field('勤務曜日に関する補足説明', $joPostId)) : ?>
                <div class="mt-3 mb-1">
                  <small class="d-block fw-bold">【勤務日補足】</small>
                  <?= str_replace("\n", '<br>', get_field('勤務曜日に関する補足説明', $joPostId)); ?>
                </div>
              <?php endif; ?>

              <?php if (get_field('休日に関する補足説明', $joPostId)) : ?>
                <div class="mt-3 mb-1">
                  <small class="d-block fw-bold">【休日補足】</small>
                  <?= str_replace("\n", '<br>', get_field('休日に関する補足説明', $joPostId)); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>


        <h4 class="d-block">仕事に関する記載事項</h4>

        <div class="mb-3">
          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">仕事内容</div>
            <div class="p-2">仕事内容<?= get_field('仕事内容', $joPostId); ?></div>
          </div>

          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">必須条件</div>
            <div class="p-2">
              <?= get_field('必須条件', $joPostId) ? str_replace("\n", '<br>', get_field('必須条件', $joPostId)) : 'なし'; ?>
            </div>
          </div>

          <?php if (get_field('優遇条件', $joPostId)) : ?>
            <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
              <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">歓迎条件</div>
              <div class="p-2">
                <?= str_replace("\n", '<br>', get_field('優遇条件', $joPostId)); ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if (get_field('採用担当者からのコメント', $joPostId)) : ?>
            <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
              <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">採用担当者からのコメント</div>
              <div class="p-2">
                <?= str_replace("\n", '<br>', get_field('採用担当者からのコメント', $joPostId)); ?>
              </div>
            </div>
          <?php endif; ?>
        </div>



        <h3 class="d-block border-bottom border-3 border-primary">応募情報</h3>

        <h4 class="d-block">掲載事業者情報</h4>

        <div class="mb-3">
          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">掲載事業者</div>
            <div class="p-2"><?= get_field('事業所名', $joPostId); ?></div>
          </div>

          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">住所</div>
            <div class="p-2">
              <div>
                <small><?= insertHyphenToZipcode(get_field('郵便番号', $joPostId)); ?></small>
              </div>
              <div><?= get_field('都道府県名', $joPostId) . get_field('住所', $joPostId); ?></div>
            </div>
          </div>

          <?php if (get_field('採用担当者', $joPostId)) : ?>
            <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
              <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">採用担当者</div>
              <div class="p-2">
                <?= str_replace("\n", '<br>', get_field('採用担当者', $joPostId)); ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">応募先</div>
            <div class="p-2">
              <?php $contact_tel = (get_field('問い合わせ先', $joPostId)) ? get_field('問い合わせ先', $joPostId) : get_field('電話番号', $joPostId); ?>
              <div>
                <?php if (str_starts_with($contact_tel, 'http')) : ?>
                  <a href="<?= $contact_tel; ?>"><?= $contact_tel; ?></a>
                <?php else : ?>
                  <a href="tel:<?= $contact_tel; ?>"><?= $contact_tel; ?></a>
                <?php endif; ?>
              </div>

              <?php if (get_field('FAX', $joPostId)) : ?>
                <div>
                  <i class="fas fa-fax text-secondary"></i>
                  <?= get_field('FAX', $joPostId); ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">応募方法</div>
            <div class="p-2">
              <?= str_replace("\n", '<br>', get_field('応募情報', $joPostId)); ?>
            </div>
          </div>
        </div>


        <h4 class="d-block">募集職種</h4>
        <div class="mb-3">
          <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
            <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">募集職種</div>
            <div class="p-2">
              <?php
              $genre    = get_the_terms(get_the_ID(), 'cat_job_offer_genre');
              $jobTypes = get_the_terms($post_id, 'cat_job_type');

              if ($jobTypes) {
                foreach ($jobTypes as $jt) {
                  echo $jt->name . '<br>';
                }
              }
              ?>
            </div>
          </div>
        </div>


      </div>
      <?php
      // シェアボタン
      get_template_part('template-parts/share-buttons'); ?>
    </div>



    <?php
    $args = array(
      'author'         => $epId,
      'post_type'      => 'job_offers',
      'posts_per_page' => -1,
    );

    $jo_posts = get_posts($args);
    // print_r($jo_posts);
    if (count($jo_posts) > 1) :
    ?>
        <div class="w-100 mx-auto">
          <div class="d-flex mb-3">
            <hr class="my-3 ms-0 me-3 border-0 bg-dark flex-grow-1" style="height:2px;">
            <h3>この事業所の求人</h3>
            <hr class="my-3 ms-3 me-0 border-0 bg-dark flex-grow-1" style="height:2px;">
          </div>

          <div class="edit-area d-flex" style="overflow-x: auto;">
            <?php
            foreach ($jo_posts as $jo_post) :
              $the_post_id = $jo_post->ID;
              if ($the_post_id == get_the_ID()) continue;
            ?>

              <div class="card card-loop" style="width:250px;min-width:250px;">
                <div class="card-body d-flex flex-column gap-2 p-2">
                  <div><span class="badge badge-sm rounded-pill bg-success"><?= get_field('契約形態', $the_post_id); ?></span></div>
                  <h5 class="card-title m-0"><?= get_the_title($the_post_id); ?></h5>

                  <div>
                    <span><?= get_field('給与形態', $the_post_id); ?></span>
                    <span>
                      <?php
                      if (intval(get_field('給与_自_', $the_post_id))) {
                        echo '¥' . number_format(get_field('給与_自_', $the_post_id));
                        if (get_field('給与_至_', $the_post_id)) echo '～';
                      }
                      ?>
                    </span>
                  </div>
                  <?php if (!empty($job_type)) : ?>
                    <small>
                      <?php if ($job_type[0]) : ?><?= $job_type[0]->name; ?><?php endif; ?><br>
                      <?php if ($job_type[1]) : ?>┗ <?= $job_type[1]->name; ?><?php endif; ?>
                    </small>
                  <?php endif; ?>
                  <a class="btn btn-primary d-block text-center mt-auto" href="<?= get_permalink($the_post_id); ?>">詳しく見る</a>
                  <small class="d-block text-end">掲載期限：<?= get_field('掲載期限', $the_post_id) ? date('Y/m/d', strtotime(get_field('掲載期限', $the_post_id))) : '無期限'; ?></small>
                </div>
              </div>

            <?php endforeach; ?>
          </div>
        </div>
    <?php endif; ?>

  </article>


