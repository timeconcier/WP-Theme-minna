<?php $post_type = $args['post_type']; ?>
<input type="hidden" name="pt" value="<?= $post_type; ?>">
<div class="px-3 py-2 mb-3" style="background-color:rgba(255,255,255, .9)">

<?php
  // ========================================================================
  // 事業所検索
  // ------------------------------------------------------------------------
  if($post_type === 'enterprises'):
    $terms = get_terms( 'cat_enterprise_genre', array(
        'hide_empty' => false,
        'parent'     => 0,
        'orderby'    => 'slug',
        'order'      => 'DESC'
    ));
    $terms = json_decode(json_encode($terms), true);
    $showTermNames = [ '教育', '飲食', '病院', '農業', '宿泊' ];

    foreach($showTermNames as $n) {
      $index 		 = array_search( $n, array_column($terms, 'name') );
      $showTerms[] = $terms[$index];
    }
  // ======================================================================== ?>
  <div class="g-ep d-flex flex-wrap gap-1 justify-content-center my-2">
    <?php foreach($showTerms as $term): ?>
      <label class="g-ep-label badge bg-outline-success rounded-pill border border-success">
        <input type="checkbox" class="me-1 d-none" name="g_ep[]" id="g_ep-<?= $term['term_id']; ?>" value="<?= urldecode($term['slug']); ?>"><?= $term['name']; ?>
      </label>
    <?php endforeach; ?>
  </div>


  <?php
    // ========================================================================
    // 求人検索
    // ------------------------------------------------------------------------
    elseif($post_type === 'job_offers'):
    // ======================================================================== ?>
    <div class="form-group">
      <!-- 市町村 -->
      <?php
      $cities = get_categories(array(
        'taxonomy'   => 'cities',
        'hide_empty' => 0,
        'orderby'   => 'ID',
        'order'     => 'ASC',
      ));
      ?>
      <select name="l" class="form-select">
        <option value="">高知県全域</option>
        <?php foreach ($cities as $city) : ?>
          <option value="<?= ($city->name == '高知県') ? '' : $city->slug; ?>">
            <?= ($city->name == '高知県') ? $city->name . '全域' : $city->name; ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

  <?php
    // ========================================================================
    // クーポン検索
    // ------------------------------------------------------------------------
    elseif($post_type === 'coupons'):
      $terms = get_categories( array(
          'taxonomy'	 => 'cat_coupon',
          'hide_empty' => 0,
          'orderby'	 => 'ID',
          'order'		 => 'ASC',
      ) );
    // ======================================================================== ?>
      <div class="d-flex flex-wrap gap-2 justify-content-center">
        <?php foreach($terms as $t): ?>
        <div class="form-check">
          <label class="form-check-label d-flex gap-1">
            <input type="checkbox" name="cp[]" class="form-check-input" value="<?= urldecode($t->name); ?>">
            <span><?= $t->name; ?></span>
          </label>
        </div>
        <?php endforeach; ?>
      </div>

  <?php
    // ========================================================================
    // イベント検索
    // ------------------------------------------------------------------------
    elseif($post_type === 'events'):
      $terms = get_categories( array(
        'taxonomy'	 => 'cat_events',
        'hide_empty' => 0,
        'orderby'	 => 'ID',
        'order'		 => 'ASC',
      ) );
    // ======================================================================== ?>
      <div class="d-flex flex-wrap gap-2 justify-content-center">
        <?php foreach($terms as $t): ?>
        <div class="form-check">
          <label class="form-check-label d-flex gap-1">
            <input type="checkbox" name="ev[]" class="form-check-input" value="<?= urldecode($t->slug); ?>">
            <span><?= $t->name; ?></span>
          </label>
        </div>
        <?php endforeach; ?>
      </div>
  <?php
    // ========================================================================
    // 上以外でカスタムタクソノミーありの検索
    // ------------------------------------------------------------------------
    elseif (taxonomy_exists('cat_'.$post_type)):
      $terms = get_categories( array(
        'exclude'     => '4828',
        'taxonomy'	  => 'cat_'.$post_type,
        'hide_empty'  => 0,
        'orderby'	    => 'ID',
        'order'		    => 'ASC',
      ) );
    // ======================================================================== ?>
      <div class="d-flex flex-wrap gap-2 justify-content-center">
        <?php foreach($terms as $t): ?>
        <div class="form-check">
          <label class="form-check-label d-flex gap-1">
            <input type="checkbox" name="tag[]" class="form-check-input" value="<?= urldecode($t->slug); ?>">
            <span><?= $t->name; ?></span>
          </label>
        </div>
        <?php endforeach; ?>
      </div>
  <?php
    // ========================================================================
    // 上以外
    // ------------------------------------------------------------------------
    else :
    // ======================================================================== ?>
      <div class="form-group mb-3">
        <label class="fw-bold" for=""><small>キーワード</small></label>
        <input type="text" class="form-control mb-3" placeholder="キーワード" value="<?php echo get_search_query(); ?>" name="s" title="検索" />
      </div>

  <?php endif; ?>

  <div class="w-100 d-flex justify-content-center mt-2 gap-3">
    <?php if (taxonomy_exists('cat_'.$post_type) or strstr(get_bloginfo('url'), 'supporter')): ?>
      <button type="button" class="btn btn-secondary bg-light px-3 py-1" style="min-width:135px;" id="show-filter">
        <span>もっと詳しく</span>
        <i id="icon-filter-caret" class="bi bi-caret-down-fill"></i>
      </button>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary px-3 py-1" style="min-width:135px;">
      <span>検索</span>
    </button>
  </div>

  <div id="filter-area" class="d-none">
    <div class="my-3">

      <?php if ($post_type !== 'post'): ?>
        <div class="form-group mb-3">
          <label class="fw-bold" for=""><small>キーワード</small></label>
          <?php $placeholder = in_array($post_type, ['post', 'parentings', 'recipes']) ? 'キーワード' : '例：◯◯市 カフェ お店'; ?>
          <input type="text" class="form-control mb-3" placeholder="<?= $placeholder; ?>" value="<?php echo get_search_query(); ?>" name="s" title="検索" />
        </div>
      <?php endif; ?>
      <!-- ■■■■■■■■■■■■■■■■■■■■■■■■■■■■ フィルター ■■■■■■■■■■■■■■■■■■■■■■■■■■■■ -->
      <?php
        // ========================================================================
        // 事業所検索
        // ------------------------------------------------------------------------
        if($post_type === 'enterprises'):
          $showTerms = [];
          $showTermNames = [
            '製造', '士業', '建設', '住まい', '不動産', '理美容', '子育て',
            'トラベル', '公共機関', '情報通信', '金融・保険', '浴場・洗濯', '卸売・小売', 'ウェディング',
            '自動車・バイク', '娯楽・スポーツ・レジャー', '整体・マッサージ', 'npo・団体', 'エンターテイメント'
          ];

          foreach($showTermNames as $n) {
            $index 		 = array_search( $n, array_column($terms, 'name') );
            $showTerms[] = $terms[$index];
          }
        // ======================================================================== ?>
        <div class="g-ep d-flex flex-wrap gap-2 justify-content-center">
          <?php foreach($showTerms as $term): ?>
            <label class="g-ep-label badge bg-outline-success rounded-pill border border-success" style="cursor:pointer;">
              <input type="checkbox" class="me-1 d-none" name="g_ep[]" id="g_ep-<?= $term['term_id']; ?>" value="<?= urldecode($term['slug']); ?>"><?= $term['name']; ?>
            </label>
          <?php endforeach; ?>
        </div>
      <?php
        // ========================================================================
        // 求人検索
        // ------------------------------------------------------------------------
        elseif($post_type === 'job_offers'):
          $terms = get_categories( array(
            'taxonomy'	 => 'cat_job_type',
            'hide_empty' => 0,
            'parent'     => 0,
            'orderby'	 => 'ID',
            'order'		 => 'DESC',
          ) );
        // ======================================================================== ?>
          <div class="form-group mb-3">
            <label class="fw-bold" for="">契約(雇用)形態</label>
            <?php $f_emp = ['正社員', '契約社員', '派遣社員', 'アルバイト・パート', '日雇い', '臨時（季節雇用）', '請負', '業務委託', 'その他の契約形態']; ?>
            <div class="container">
              <div class="row">
                <?php foreach($f_emp as $f): ?>
                <div class="form-check col-6 col-sm-4 col-md-3">
                  <label class="form-check-label">
                  <input type="checkbox" name="f_emp[]" class="form-check-input" value="<?= $f; ?>">
                  <?= $f; ?>
                  </label>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>

          <div class="form-group mb-3">
            <select name="jt" class="form-select">
              <option value="" selected>- 職種 -</option>
              <?php foreach($terms as $term){ ?><option value="<?= urldecode($term->slug); ?>"><?= $term->name; ?></option><?php } ?>
            </select>
          </div>

          <div class="form-group mb-3">
            <label class="fw-bold" for="">給与</label>
            <div class="d-flex flex-column flex-sm-row">
              <div class="mb-2">
                <select name="f_sly" class="form-select" style="width:initial;">
                  <option value="">- 給与形態 -</option>
                  <option value="年収">年収</option>
                  <option value="月給">月給</option>
                  <option value="日給">日給</option>
                  <option value="時給">時給</option>
                </select>
              </div>
              <div class="input-group mb-2">
                <input type="number" class="form-control" name="f_amt" min="800" placeholder="給与形態を選択してください。" disabled>
                <span class="input-group-text">円～</span>
              </div>
            </div>
          </div>

          <div class="form-group mb-3">
            <label class="fw-bold" for="">勤務時間</label>
            <div class="input-group mb-3">
              <input type="time" class="form-control icon-del" name="f_time_start">
              <span class="input-group-text">～</span>
              <input type="time" class="form-control icon-del" name="f_time_end">
            </div>
          </div>

          <div class="form-group">
            <?php
              $g_joTermParents = get_terms('cat_job_offer_genre', array('parent' => 0, 'hide_empty' => false));
              $g_joTerms 		 = get_terms('cat_job_offer_genre', array('hide_empty' => false));
              foreach($g_joTermParents as $parent):
            ?>
              <div class="mb-2">
                <label class="d-block w-100 px-2 py-1 mb-2" style="background-color:#f5f5ff;" for=""><?= $parent->name; ?></label>
                <div class="container">
                  <div class="row">
                    <?php foreach($g_joTerms as $term): if($parent->term_id == $term->parent): ?>
                      <div class="form-check col-6 col-sm-4 col-md-3">
                        <label class="form-check-label">
                          <input type="checkbox" name="g_jo[]" class="form-check-input" value="<?= $term->name; ?>">
                          <?= $term->name; ?>
                        </label>
                      </div>
                    <?php endif; endforeach; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

      <?php
        // ========================================================================
        // クーポン検索
        // ------------------------------------------------------------------------
        elseif($post_type === 'coupons'):
        // ======================================================================== ?>

          <label class="fw-bold" for="f_cp_start"><small>利用期間</small></label>
          <div class="form-group mb-3 d-flex flex-column flex-sm-row gap-1">
            <div class="d-flex align-items-end gap-1">
              <input type="date" name="f_cp_start" id="f_cp_start" class="form-control" value="<?= date('Y-m-d'); ?>">
              <small style="white-space: nowrap;">から</small>
            </div>

            <div class="d-flex align-items-end gap-1">
              <input type="date" name="f_cp_end" id="f_cp_end" class="form-control">
              <small style="white-space: nowrap;">まで</small>
            </div>
          </div>

      <?php
        // ========================================================================
        // イベント検索
        // ------------------------------------------------------------------------
        elseif($post_type === 'events'):
        // ======================================================================== ?>
          <label class="fw-bold" for="f_ev_start"><small>開催期間</small></label>
          <div class="form-group mb-3 d-flex flex-column flex-sm-row gap-1">
            <div class="d-flex align-items-end gap-1">
              <input type="date" name="f_ev_start" id="f_ev_start" class="form-control" value="<?= date('Y-m-d'); ?>">
              <small style="white-space: nowrap;">から</small>
            </div>
            <div class="d-flex align-items-end gap-1">
              <input type="date" name="f_ev_end" id="f_ev_end" class="form-control">
              <small style="white-space: nowrap;">まで</small>
            </div>
          </div>

        <?php
        // ========================================================================
        // 標準
        // ------------------------------------------------------------------------
        elseif($post_type === 'post'):
        // ======================================================================== ?>
          <?php
            if (strstr(get_bloginfo('url'), 'supporter')) :
              $category = get_terms('category', array('hide_empty' => false));
              $t_prt    = get_terms('tag_parentings', array('hide_empty' => false));
              $t_rcp    = get_terms('tag_recipes', array('hide_empty' => false));
          ?>

            <div class="form-group">
                <div class="mb-2">
                  <label class="d-block w-100 px-2 py-1 mb-2" style="background-color:#f5f5ff;" for="">口コミカテゴリー</label>
                  <div class="container">
                    <div class="row">
                      <?php
                        foreach($category as $term): if($parent->term_id == $term->parent): ?>
                        <div class="form-check col-6 col-sm-4 col-md-3">
                          <label class="form-check-label">
                            <input type="checkbox" name="c[]" class="form-check-input" value="<?= $term->slug; ?>">
                            <?= $term->name; ?>
                          </label>
                        </div>
                      <?php endif; endforeach; ?>
                    </div>
                  </div>
                </div>

                <?php if(count($t_rcp)): ?>
                  <div class="mb-2">
                    <label class="d-block w-100 px-2 py-1 mb-2" style="background-color:#f5f5ff;" for="">子育てタグ</label>
                    <div class="container">
                      <div class="row">
                        <?php
                          foreach($t_prt as $term): if($parent->term_id == $term->parent): ?>
                          <div class="form-check col-6 col-sm-4 col-md-3">
                            <label class="form-check-label">
                              <input type="checkbox" name="t_prt[]" class="form-check-input" value="<?= $term->slug; ?>">
                              <?= $term->name; ?>
                            </label>
                          </div>
                        <?php endif; endforeach; ?>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if(count($t_rcp)): ?>
                  <div class="mb-2">
                    <label class="d-block w-100 px-2 py-1 mb-2" style="background-color:#f5f5ff;" for="">料理・レシピタグ</label>
                    <div class="container">
                      <div class="row">
                        <?php
                          foreach($t_rcp as $term): if($parent->term_id == $term->parent): ?>
                          <div class="form-check col-6 col-sm-4 col-md-3">
                            <label class="form-check-label">
                              <input type="checkbox" name="t_rcp[]" class="form-check-input" value="<?= $term->slug; ?>">
                              <?= $term->name; ?>
                            </label>
                          </div>
                        <?php endif; endforeach; ?>
                      </div>
                    </div>
                  </div>
              <?php endif; ?>
            </div>

          <?php endif; ?>
      <?php endif; ?>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-primary py-2" style="min-width:150px;">検索</button>
    </div>

  </div>

</div>