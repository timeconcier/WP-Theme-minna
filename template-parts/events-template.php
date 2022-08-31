<?php
$thumbnail  = (has_post_thumbnail()) ? get_the_post_thumbnail_url() : wp_get_attachment_url(NO_IMAGE_ID);
$epId       = get_the_author_meta('ID');

// 事業所詳細 投稿情報取得
$post_array  = is_single() ? get_post() : get_post($_GET['id']);
$epPostId    = $post_array->ID;
$epName      = get_the_author_meta('事業所名');
$epUrl       = get_the_author_meta('サイトurl');
$epPermalink = get_post_permalink($epPostId);
$outsideLink = get_the_author_meta('外部リンクを使用');
$eventTerms  = get_the_terms(get_the_ID(), 'cat_events');
$title       = is_single() ? get_the_title() : get_the_title($_GET['id']);
?>

<?php if (get_post_meta($epPostId, 'イベント中止', true)) : ?>
<div class="bg-danger text-white py-2 px-3 mb-3">
<h2 class="text-white m-0 h4" style="">【中止】<?= $title; ?></h2>
<?php else : ?>
<div class="bg-dark text-white py-2 px-3 mb-3">
<h2 class="text-white m-0 h4" style=""><?= $title; ?></h2>
<?php endif; ?>
</div>
<article class="mb-5 px-2">

  <div class="w-100 d-sm-flex mx-auto align-items-end  mb-3" style="max-width:800px;">

    <?php
    if ($mediaIds = get_post_meta($epPostId, 'ギャラリー', true) and count($mediaIds)) :
      $mediaIds = array_unique($mediaIds);
      foreach ($mediaIds as $mediaId) :
        $mediaUrl = wp_get_attachment_url($mediaId);
    ?>

        <div class="px-1 mb-2 mx-auto">
          <img src="<?= $mediaUrl; ?>" alt="" class="d-block mx-auto w-100" style="max-width:500px;max-height:500px;">
        </div>

    <?php endforeach;
    endif; ?>
  </div>

  <?php if (get_post_meta($epPostId, 'イベント中止', true)) : ?>
    <div class="bg-danger text-white py-2 px-3 mb-3">
      <h5 class="text-white m-0 text-center">このイベントは中止になりました。</h2>
    </div>
  <?php else : ?>
    <?php // シェアボタン
    get_template_part('template-parts/share-buttons'); ?>
  <?php endif; ?>
  <div><?= str_replace(["\n", "\r\n"], '<br>', get_post_meta($epPostId, 'イベント情報', true)); ?></div>


  <div class="py-2 mx-auto w-100 d-flex flex-wrap" style="max-width:800px;">
    <?php if ($eventTerms and count($eventTerms) > 0) : foreach ($eventTerms as $term) : ?>
        <span class="badge rounded-pill fw-normal bg-warning m-1 px-3 py-1"><?= $term->name; ?></span>
    <?php endforeach;
    endif; ?>
  </div>

  <div class="list-group list-group-flush border-top border-bottom">
    <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
      <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">開催日時</div>
      <div class="p-2">
        <?php
        $startDate = (get_post_meta($epPostId, '開催日_自_', true)) ? date('Y年n月j日', strtotime(get_post_meta($epPostId, '開催日_自_', true))) : '';
        $endDate   = (get_post_meta($epPostId, '開催日_至_', true)) ? date('Y年n月j日', strtotime(get_post_meta($epPostId, '開催日_至_', true))) : '';
        if ($startDate or $endDate) {
          echo ($startDate == $endDate) ? $startDate : $startDate . '～' . $endDate;
          echo '<br>';
        } else {
          echo '無期限';
        }
        ?>

        <?php
        if (get_post_meta($epPostId, '開催時間_自_', true) and get_post_meta($epPostId, '開催時間_至_', true)) {
          echo date('G:i', strtotime(get_post_meta($epPostId, '開催時間_自_', true))) . ' ～ ' . date('G:i', strtotime(get_post_meta($epPostId, '開催時間_至_', true)));
        }
        ?>
        <?php if (get_post_meta($epPostId, '開催日時詳細', true)) : ?>
          <div>
            <small class="fw-bold">【詳細】</small><br>
            <?= str_replace("\n", '<br>', get_post_meta($epPostId, '開催日時詳細', true)); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
      <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">開催場所</div>
      <div class="p-2">
        <?= (get_post_meta($epPostId, '開催場所', true)) ? get_post_meta($epPostId, '開催場所', true) : $epName; ?>
      </div>
    </div>

    <?php if (get_post_meta($epPostId, '定員', true)) : ?>
      <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
        <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">定員</div>
        <div class="p-2"><?= get_post_meta($epPostId, '定員', true); ?>名</div>
      </div>
    <?php endif; ?>

    <?php if (get_post_meta($epPostId, '料金', true)) : ?>
      <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
        <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">料金</div>
        <div class="p-2">
          <?= str_replace(["\n", "\r\n"], '<br>', get_post_meta($epPostId, '料金', true)); ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (get_post_meta($epPostId, 'お問い合わせ', true)) : ?>
      <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
        <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">お問合せ情報</div>
        <div class="p-2">
          <?php
          $contact_text = get_post_meta($epPostId, 'お問い合わせ', true);
          $pattern = '/((?:https?|ftp):\/\/[-_.!~*\'()a-zA-Z0-9;\/?:@&=+$,%#]+)/';
          $replace = '<a href="$1">$1</a>';
          echo str_replace(["\n", "\r\n"], '<br>', preg_replace($pattern, $replace, $contact_text));
          ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (get_post_meta($epPostId, '交通アクセス', true)) : ?>
      <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
        <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">交通アクセス</div>
        <div class="p-2">
          <?php
          $access_text = get_post_meta($epPostId, '交通アクセス', true);
          echo str_replace(["\n", "\r\n"], '<br>', preg_replace($pattern, $replace, $access_text));
          ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
      <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">駐車場</div>
      <div class="p-2"><?= get_post_meta($epPostId, '駐車場有無', true) ? 'あり' : 'なし'; ?></div>
    </div>

    <?php if (get_post_meta($epPostId, 'ホームページ', true)) : ?>
      <div class="list-group-item d-flex flex-column flex-sm-row align-items-stretch p-0">
        <div class="p-2 fw-bold" style="min-width:150px;background-color:#ECEFF1;">ホームページ</div>
        <div class="p-2">
          <a href="<?= get_post_meta($epPostId, 'ホームページ', true); ?>"><?= get_post_meta($epPostId, 'ホームページ', true); ?></a>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <div class="text-end">
    <?php
    $epPost = get_posts(array(
      'post_type'   => 'enterprises',
      'author' => $post_array->post_author
    ))[0];
    if ($epPost) {
      echo '掲載事業者：<a href="' . $epPost->guid . '">' . $epPost->post_title . '</a>';
    }
    ?>
  </div>


  <?php if (!get_post_meta($epPostId, 'イベント中止', true)) : ?>
    <div class="mt-3">
      <?php
      // シェアボタン
      get_template_part('template-parts/share-buttons'); ?>
    </div>
  <?php endif; ?>
</article>

<?php if (get_post_meta($epPostId, '開催地住所', true)) : ?>
  <iframe src="https://www.google.com/maps?output=embed&q=<?= get_post_meta($epPostId, '開催場所', true); ?>&z=16" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
<?php endif; ?>

