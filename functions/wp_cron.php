<?php

/** *********************************************************************
 * 投稿タイプ更新
 * - 求人・クーポン等、規定の日になると表示されないようにする
 ********************************************************************* */
add_action('schedule_post_status_update', 'post_status_update');
function post_status_update()
{
  $today = date('Y-m-d', strtotime('+9 hours'));
  $now   = date('H:i', strtotime('+9 hours'));
  if ($now != '00:00') return;

  $posts_args = array(
    // 求人情報
    array(
      'appId' => KINTONE_JOB_OFFER,
      'args'  => array(
        'posts_per_page' => -1,
        'post_type'      => 'job_offers',
        'meta_key'       => '掲載期限',
        'meta_value'     => $today,
        'meta_compare'   => '<',
        'orderby'        => 'meta_value',
        'post_status'    => 'publish',
      ),
      'update' => array(
        '求人ステータス' => array('value' => '終了'),
      )
    ),
    // イベント
    array(
      'appId' => KINTONE_EVENT,
      'args'  => array(
        'posts_per_page' => -1,
        'post_type'      => 'events',
        'meta_key'       => '開催日_至_',
        'meta_value'     => $today,
        'meta_compare'   => '<',
        'orderby'        => 'meta_value',
        'post_status'    => 'publish',
      ),
      'update' => array(
        'イベントステータス' => array('value' => '終了'),
      )
    )
  );

  $header = array(
    'X-Kintone-Url: '. KINTONE_BASE_URL,
    'X-Kintone-Username: '. KINTONE_USERNAME,
    'X-Kintone-Password: '. KINTONE_PASSWORD,
  );

  foreach ($posts_args as $post) {

    // 該当の投稿をすべて取得
    $posts   = get_posts($post['args']);
    // print_r($posts);

    foreach ($posts as $p) {
      // カスタムフィールド「レコード番号」に値があれば
      // レコード番号配列に追加
      $results = array();
      if ($rec_id = get_field('レコード番号', $p->ID)) {
        $results['post_id'] = $p->ID;
        $results['rec_id']  = $rec_id;
        // $results['kt_result'] = $client->record()->put($post['appId'], intval($rec_id), $post['update']);

        // kintone更新
        $results['kt_result'] = exec_curl(
          KINTONE_API_URL.'/record/upsetRecord',
          'POST',
          array(
            'app' => $post['appId'],
            'updateKey' => array(
              'field' => 'レコード番号',
              'value' => intval($rec_id),
            ),
            'record' => $post['update']
          ),
          $header
        )['response'];

        // 投稿ステータスを「非表示」に設定
        $results['wp_result'] = wp_update_post(array(
          'ID'          => $p->ID,
          'post_status' => 'private',
        ), true);
      }
    }

    file_put_contents(dirname(__DIR__) . '/log/wp_cron.log', "['{$today} {$now}'] " . json_encode($results, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
  }
}
// post_status_update();


/** *********************************************************************
 * 求人期限1週間前メール送信
 * - 該当の求人投稿内容を取得し、投稿者IDのユーザーにメール送信
 * 実行間隔     ：hourly
 * 送信時刻指定 ：13:00
 ********************************************************************* */
add_action('schedule_job_offer_limit_send_message', 'job_offer_limit_send_message');
function job_offer_limit_send_message()
{
  add_filter('wp_mail_content_type', function () {
    return 'text/html';
  });

  $member_url   = 'https://member.digital-town.jp';
  $now          = date('H:i', strtotime('+9 hours'));
  $today        = date('Y-m-d', strtotime('+9 hours'));
  $a_week_later = date("Y-m-d", strtotime("{$today} 1 week"));

  if ($now != '13:00') return;     // 13時以外は送信しない。

  $posts = get_posts(array(
    'posts_per_page'   => -1,
    'post_type'        => 'job_offers',
    'post_status'      => 'publish',
  ));

  $headers[] = array(
    'Content-type: text/html; charset=UTF-8',
  );

  foreach ($posts as $n => $post) {
    if ($n != 0) continue;

    $post_id      = $post->ID;
    $post_url     = get_permalink($post_id);
    $limit_date   = get_field('掲載期限', $post_id);
    $author_email = get_the_author_meta('user_email', $post->post_author);
    $ep_name      = get_the_author_meta('事業所名', $post->post_author);

    if ($a_week_later == $limit_date) {
      // $to[] = 'k.nishizoe@timeconcier.jp';
      $to[] = $author_email;
      // ---------------------------------------------
      // 掲載者宛に登録通知メールを送る
      // ---------------------------------------------
      $subject   = '【ご対応願い】（延長／取り下げ）とさジョブ　求人情報掲載期限について - 【デジタル町一丁目】みんなのまちサイト';
      $message = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd">
<html lang="ja">
    <head>
        <meta name="viewport" content="target-densitydpi=device-dpi,width=device-width,maximum-scale=1.0,user-scalable=yes">
        <meta http-equiv="Content-Language" content="ja">
        <meta charset="shift_jis">
    </head>
    <body>
        {$ep_name}　ご担当者様<br><br>
        <p>
            デジタル町一丁目・お客様サポートです。<br>
            平素は弊社のサービスをご活用いただき、<br>
            誠にありがとうございます。
        </p>
        <p>
            貴社が「とさジョブ」で公開している求人情報について、<br>
            「<a href="$post_url">{$post->post_title}</a>」の求人票が、<br>
            当初設定された掲載期限日の一週間前になりました。<br>
            下記いずれかのご対応をお願いいたします。<br>
            <p>
                ・掲載延長を希望される場合・・・日付の更新<br>
                ・終了される場合　　　　　・・・取り下げの申請
                （何もしない場合は、掲載期限日に終了となります）<br>
            </p>
            お手続きはこちらからどうぞ→<a href="{$member_url}">事業者マイページへ</a>
        </p>
        <p>
            なお、このメールアドレスは送信専用のためご返信いただけません。<br>
            ご不明な点などございましたら、下記まで<br>
            お問合せいただきますようお願い申し上げます。<br>
            　→<a href="https://tosajob.jp/#contact">お問合せフォームへ</a>
        </p>
        <p>今後も「デジタル町一丁目」をどうぞよろしくお願いいたします。</p>

        <p>
            ＜＞─＜＞─＜＞─＜＞─＜＞─＜＞─＜＞─＜＞─＜＞<br>
            アニバーサリーコンシェル株式会社<br>
            「デジタル町一丁目」お客様サポート<br>
            <br>
            ──────────────────────────<br>
            <br>
            メール：info@digital-town.jp<br>
            電話  ：088-832-1221 (平日10:00～17:30)<br>
            ＜＞─＜＞─＜＞─＜＞─＜＞─＜＞─＜＞─＜＞─＜＞<br>
        </p>
    </body>
</html>
EOD;
      wp_mail($to, $subject, $message, implode("\r\n", $headers));
    }
  }
}




/** *********************************************************************
 * 「非公開」ステータスに変更されてから1ヶ月後の投稿を削除
 * - 該当の求人投稿内容を取得し、投稿者IDのユーザーにメール送信
 * 実行間隔     ：hourly
 * 送信時刻指定 ：13:00
 ********************************************************************* */
add_action('schedule_private_posts_delete', function () {
  ini_set("allow_url_fopen", true);
  if (get_current_user_id() == 1) {
    $post_types = array(
       // WP 投稿タイプ => kintone アプリID
      'job_offers' => array('id' => KINTONE_JOB_OFFER,  'name' => '求人'),
      'coupons'    => array('id' => KINTONE_COUPON,     'name' => 'クーポン'),
      'events'     => array('id' => KINTONE_EVENT,      'name' => 'イベント'),
    );

    // ∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞
    // 投稿タイプをループ
    // ∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞
    foreach($post_types as $pt => $p) {
      // =======================================
      // kintoneから公開終了レコードを取得
      // =======================================
      $data = array(
        'app'       => $p['id'],
        'condition' => $p['name'].'ステータス in ("終了")',
      );
      $header = array(
        'X-Kintone-Url: ' . KINTONE_BASE_URL,
        'X-Kintone-Username: '. KINTONE_USERNAME,
        'X-Kintone-Password: '. KINTONE_PASSWORD,
      );
      $get = exec_curl(KINTONE_API_URL.'/record/getAllRecords', 'GET', $data, $header);
      $exists_rec_ids = array();
      // ∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞
      // kintoneのレコードをループ
      // ∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞
      foreach(json_decode($get['response'], true) as $rec) {
        // ---------------------------------------
        // レコード番号のみの配列を作成
        // ---------------------------------------
        if (isset ($rec['$id'])) $exists_rec_ids[] = $rec['$id']['value'];
      }

      // レコード番号が空なら以降スキップ
      if (count($exists_rec_ids) == 0) continue;


      // =======================================
      // WordPressから公開終了レコードを取得
      // =======================================
      $posts = get_posts(array(
        'posts_per_page' => -1,
        'post_type'      => $pt,
        'post_status'    => 'private',
        'date_query'     => array(
          array(
            'before' => date('Y/m/d',strtotime('-1 week')),
          ),
        ),
      ));


      $rec_ids = array();
      // ∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞
      // WordPressで取得した投稿をループ
      // ∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞
      foreach($posts as $i => $q) {
        $rec_id = get_field('レコード番号', $q->ID);
        // ---------------------------------------
        // レコード番号でレコードの有無を検証
        // ---------------------------------------
        if ($rec_id and is_numeric($rec_id)) {
          if (in_array($rec_id, $exists_rec_ids)) {
            // wp_delete_post(11486, true);
            // 最大1,000件ごとに格納
            $rec_ids[floor($i / 1000)][] = array('id' => $rec_id);
          }
        }
      }
      // $ret[$pt] = $post_ids;
      // print_r($rec_ids);

      // ∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞
      // 絞り込んだレコード番号をループ
      // ∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞∞
      file_put_contents(dirname(__DIR__) . '/log/wp_cron.log', PHP_EOL.'分割数: '.count($rec_ids), FILE_APPEND);
      foreach($rec_ids as $rids) {
        $count = count($rids);
        // echo "{$pt}({$p['id']}): {$count}件".PHP_EOL;
        file_put_contents(dirname(__DIR__) . '/log/wp_cron.log', PHP_EOL."{$pt}({$p['id']}): {$count}件", FILE_APPEND);
        // print_r($rids);

        $data = array(
          'app'     => $p['id'],
          'records' => $rids,
        );
        $header = array(
          'X-Kintone-Url: '. KINTONE_BASE_URL,
          'X-Kintone-Username: '. KINTONE_USERNAME,
          'X-Kintone-Password: '. KINTONE_PASSWORD,
        );

        $delete = exec_curl(KINTONE_API_URL.'/record/deleteAllRecords', 'DELETE', $data, $header);
        // print_r($delete.PHP_EOL);
        file_put_contents(dirname(__DIR__) . '/log/wp_cron.log', PHP_EOL."{$delete}", FILE_APPEND);
      }
    }
  }
});


// テスト時実行
// do_action('schedule_private_posts_delete');

add_action('wp', function () {
  /** ******************************************************************************************************************************************
   * 投稿タイプ更新
   * -------------------------------------------------------------------------------------------------------------------------------------------
   * - 求人・クーポン等、規定の日になると表示されないようにする
   ****************************************************************************************************************************************** */
  if (!wp_next_scheduled('schedule_post_status_update'))  wp_schedule_event(time(), 'every_minute', 'schedule_post_status_update');

  /** ******************************************************************************************************************************************
   * 求人期限1週間前メール送信
   * -------------------------------------------------------------------------------------------------------------------------------------------
   * - 該当の求人投稿内容を取得し、投稿者IDのユーザーにメール送信
   * 実行間隔     ： hourly
   * 送信時刻指定 ： 13:00
   ****************************************************************************************************************************************** */
  if (!wp_next_scheduled('schedule_job_offer_limit_send_message'))  wp_schedule_event(time(), 'hourly', 'schedule_job_offer_limit_send_message');
});

/** ***********************************************************
 * cURL実行
 * @param string $url     - URL
 * @param string $method  - HTTPメソッド
 * @param array  $data    - 送信データ
 * @param string $headers - ヘッダー情報
 * @return array
 *********************************************************** */
function exec_curl ($url, $method, $data, $headers) {
  $data = http_build_query($data);
  $ch   = curl_init();
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  if ($headers) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  }
  switch($method) {
    case 'GET':
    case 'get':
    case 'DELETE':
    case 'delete':
      if ($data) {
        curl_setopt($ch, CURLOPT_URL, $url.'?'.$data);
      } else {
        curl_setopt($ch, CURLOPT_URL, $url);
      }
      curl_getinfo($ch);
      break;

    case 'POST':
    case 'post':
    case 'PUT':
    case 'put':
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  //データの配列を設定する
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      break;
  }

  $info     = curl_getinfo($ch);
  $response = curl_exec($ch);
  curl_close($ch);

  return array(
    'http_code' => $info['http_code'],
    'response' => $response,
  );
}