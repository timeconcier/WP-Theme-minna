<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept");
header("Access-Control-Allow-Methods: GET, POST");
if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
  exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST)) {
  $_REQUEST = json_decode(file_get_contents('php://input'), true);
}
//00000000000000000000000000000000000000000000000000000000000000000000
// ユーザー情報
//00000000000000000000000000000000000000000000000000000000000000000000
/** ***************************************
 * create_user
 * @param String email    - 【必須】メールアドレス
 * @param String username - 【必須】ユーザーID
 * @param String password - 【必須】パスワード
 * @param String meta     - カスタムフィールド
 **************************************** */
add_action('wp_ajax_createUser', 'createUser');
add_action('wp_ajax_nopriv_createUser', 'createUser');
function createUser() {
  $req      = $_REQUEST;
  if (!$req['meta']) $req['meta'] = array();
  $email    = $req['email'];
  $username = $req['username'];
  $password = $req['password'];
  $meta     = $req['meta'];


  try {
    $userExist = get_user_by('email', $email);

    if ($userExist) {
      throw new Exception();
      return;
    } else {
      $userId = wp_create_user($username, $password);

      // エラーで帰ってきていないか再確認
      if ($userId->errors) {
        throw new Exception();
        return;
      }

      if (!empty($meta)) {
        $args = array('ID' => $userId);
        foreach ($meta as $key => $val) {
          $args[$key] = $val;
        }

        $user_data = wp_update_user($args);
      }

      userCreate_sendEmail($userId, $req);
      echo json_encode($userId, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
  } catch (Exception $e) {
    echo json_encode(['error' => '既にユーザーが存在します。'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  }
  die();
}


/** ***************************************
 * get_user
 * @param Number lineId  - 【必須】LINEユーザーID
 **************************************** */
add_action('wp_ajax_getUserInfoByLINE', 'getUserInfoByLINE');
add_action('wp_ajax_nopriv_getUserInfoByLINE', 'getUserInfoByLINE');
function getUserInfoByLINE() {
  $req    = $_REQUEST;
  $lineId = $req['lineId'];


  $args = array(
    'meta_key'     => 'line_id',
    'meta_value'   => $lineId,
    'meta_compare' => '=',

  );
  $user_query = new WP_User_Query($args);

  $user_data = $user_query->get_results();
  $user_data = count($user_data) > 0 ? $user_data[0]->data : null;
  $user_meta = get_user_meta($user_data->ID, '', false);

  // $user_meta を $user_data にマージ
  foreach ($user_meta as $key => $value) {
    if( preg_match('/^\_/', $key) ) {
      $acf_key = substr($key, 1);
      $user_data->$acf_key = $user_meta[$acf_key][0];
    }
  }

  if ($user_data) {
    $user_data->id = $user_data->ID;
    // プロパティ排除
    unset($user_data->ID);
    foreach ($user_data as $key => $value) {
      if (
        // 先頭文字
        preg_match('/^um_/',        $key) or
        // 末尾文字
        preg_match('/_pass$/',      $key)
        // 含有文字
      ) {
        unset($user_data->$key);
      }
    }
  }

  echo json_encode($user_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}


/** ***************************************
 * get_user
 * @param Number uid    - ユーザーID
 * @param Number field  - 【条件必須 - ユーザーID不明の場合】id | email | slug | login
 * @param Number value  - 【条件必須 - ユーザーID不明の場合】検索値
 **************************************** */
add_action('wp_ajax_getUserInfo', 'getUserInfo');
add_action('wp_ajax_nopriv_getUserInfo', 'getUserInfo');
function getUserInfo() {
  $req   = $_REQUEST;
  $uid   = $req['uid'];
  $field = $req['field'];
  $value = $req['value'];

  if (!$uid && $field) {
    $user = get_user_by($field, $value);
    if (!$user) {
      return;
    }
    $uid  = $user->ID;
  }

  $info = get_userdata($uid);
  $acf  = get_fields("user_{$uid}");

  $ret  = [
    'id'    => $info->ID,
    'name'  => $info->data->display_name,
    'email' => $info->data->user_email,
    'slug'  => $info->data->user_nicename,
    'acf'   => $acf
  ];

  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}

/** ***************************************
 * get_user_by
 * @param String by     - 【必須】ユーザーメタフィールド（id | ID | slug | email | login）
 * @param String value  - 【必須】値
 *************************************** */
add_action('wp_ajax_getUserInfoBy', 'getUserInfoBy');
add_action('wp_ajax_nopriv_getUserInfoBy', 'getUserInfoBy');
function getUserInfoBy() {
  $req    = $_REQUEST;
  $by     = $req['by'];
  $value  = $req['value'];
  $ret    = get_user_by($by, $value);

  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}

/** ***************************************
 * get_supporter_info
 * ログイン中のサポーター情報を取得する
 *************************************** */
add_action('wp_ajax_getSupporterInfo', 'getSupporterInfo');
add_action('wp_ajax_nopriv_getSupporterInfo', 'getSupporterInfo');
function getSupporterInfo() {
  $user = wp_get_current_user();
  $ret = [
    'name' => $user->display_name,
  ];

  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}

/** ***************************************
 * update_user
 * @param Number uid - 【必須】ユーザーID
 * @param Object acf - 【必須】カスタムフィード
 **************************************** */
add_action('wp_ajax_updateUserInfo', 'updateUserInfo');
add_action('wp_ajax_nopriv_updateUserInfo', 'updateUserInfo');
function updateUserInfo() {
  $req  = $_REQUEST;
  $uid  = $req['uid'];
  $acf  = $req['acf'];
  $meta = $req['meta'];

  try {
    // メタフィールド更新
    if ($meta) {
      $args = array('ID' => $uid);
      foreach ($meta as $key => $val) {
        $args[$key] = $val;
      }

      $user_data = wp_update_user($args);

      // foreach($meta as $key => $val){
      //     update_user_meta( $uid, $key, $val );
      // }
    }

    // ACF更新
    if ($acf) {
      foreach ($acf as $key => $val) {
        update_field($key, $val, "user_{$uid}");
      }
    }
  } catch (Exception $e) {
    echo '保存エラー: ',  $e->getMessage(), "\n";
    exit;
  }
  echo json_encode([], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}

/** ***************************************
 * update_email
 * @param Number uid - 【必須】ユーザーID
 * @param Object acf - 【必須】カスタムフィード
 **************************************** */
add_action('wp_ajax_updateEmail', 'updateEmail');
add_action('wp_ajax_nopriv_updateEmail', 'updateEmail');
function updateEmail() {
  global $wpdb;
  $req    = $_REQUEST;
  $uid    = $req['uid'];
  $email  = $req['email'];

  try {
    $sql = "UPDATE `{$wpdb->users}` SET `user_login`='{$email}' WHERE `id`='{$uid}'";

    $ret = $wpdb->update(
      $wpdb->users,               // table
      ['user_login' => $email],   // set
      ['id' => $uid]              // where
    );
  } catch (Exception $e) {
    print_r($e);
    exit;
  }
  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}

/** ***************************************
 * update_password
 * @param Number uid      - 【必須】ユーザーID
 * @param String password - 【必須】新しいパスワード
 **************************************** */
add_action('wp_ajax_updatePassword', 'updatePassword');
function updatePassword() {
  $req      = $_REQUEST;
  $uid      = $req['uid'];
  $password = $req['password'];

  try {
    wp_set_password($password, $uid);
  } catch (Exception $e) {
    echo '保存エラー: ',  $e->getMessage(), "\n";
    exit;
  }
  die();
}


/** ***************************************
 * user_login
 * @param String log       - 【必須】ユーザーID
 * @param String pwd       - 【必須】パスワード
 * @param Boolean remember - ログイン情報を保持
 **************************************** */
add_action('wp_ajax_nopriv_userLogin', 'userLogin');
function userLogin() {
  $_POST = [];
  $req   = $_REQUEST;
  $_POST['log']      = $req['log'];
  $_POST['pwd']      = $req['pwd'];
  $_POST['remember'] = $req['remember'];

  try {
    $login = wp_signon();
  } catch (Exception $e) {
    echo 'ログインエラー: ',  $e->getMessage(), "\n";
    exit;
  }
  echo json_encode($login, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}


/** ***************************************
 * user_logout
 * @param String log       - 【必須】ユーザーID
 * @param String pwd       - 【必須】パスワード
 * @param Boolean remember - ログイン情報を保持
 **************************************** */
add_action('wp_ajax_userLogout', 'userLogout');
add_action('wp_ajax_nopriv_userLogout', 'userLogout');
function userLogout() {
  try {
    wp_logout();
  } catch (Exception $e) {
    echo 'ログアウトエラー: ',  $e->getMessage(), "\n";
    exit;
  }
  die();
}


/** ***************************************
 * send_changePasswordLinkMail
 * @param Number email - 【必須】メールアドレス
 **************************************** */
add_action('wp_ajax_changePasswordLinkMail', 'changePasswordLinkMail');
function changePasswordLinkMail() {
  $req    = $_REQUEST;
  $email  = $req['email'];

  $uid  = email_exists($email);
  if ($uid) {
    $data  = get_userdata($uid);
    $acf   = get_fields("user_{$uid}");
    $email = $data->user_email;

    // $now     = date_i18n("Y-m-d H:i:s");
    // $nowStr  = date('YmdHis', strtotime("{$now} +1 hours"));
    // $now_md5 = md5(date('Y-m-d H', strtotime("{$now} +1 hours")));
    $ts = time();

    $enterprise = $acf['事業所名'];
    $personName = $acf['担当者名'];
    // ---------------------------------------------
    //ユーザー宛にパスワード変更通知メールを送る
    // ---------------------------------------------
    $subject = '【デジタル町一丁目】みんなのまちサイト - パスワードリセットのお知らせ';
    $message = <<<EOD
{$enterprise}
{$personName} 様

「みんなのまちサイト」事業者ページのパスワードリセットの申請を受け付けました。
パスワードの再設定をご希望の場合は、以下URLをクリックし新しいパスワードをご登録ください。

※ パスワードリセットの申請に心当たりがない場合は、以降の対応は不要となります。


　▼ パスワードの再設定URL
　https://minna.digital-town.jp/change-pw?u={$uid}&t={$ts}
※ このメールを受信してから1時間以内に再設定してください。


本メールに心当たりが無い場合は破棄をお願いいたします。
送信専用メールアドレスのため、直接の返信はできません。

────────────────────────────────────────────────────

アニバーサリーコンシェル株式会社
デジタル町一丁目

メール：info@digital-town.jp
電話  ：088-832-1221 (平日10:00～17:30)

────────────────────────────────────────────────────
EOD;
    // $headers[] = "Bcc: info@digital-town.jp"; // wp_mail第4引数
    wp_mail($email, $subject, $message);

    $ret = [
      'status'  => 'success',
      'email'   => $email,
      'message' => 'パスワードリセットメールを送信しました。'
    ];
    echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  } else {
    $ret = [
      'status'  => 'error',
      'message' => '該当のメールアドレスで登録されているユーザーが見つかりませんでした。'
    ];
    echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  }
  die();
}


/** ***************************************
 * delete_user
 * @param Number uid      - 【必須】ユーザーID
 * @param Number reassign - 投稿の再割当先ユーザーID
 **************************************** */
add_action('wp_ajax_deleteUser', 'deleteUser');
add_action('wp_ajax_nopriv_deleteUser', 'deleteUser');
function deleteUser() {
  $req      = $_REQUEST;
  $uid    = $req['uid'];
  $reassign = (isset($req['reassign'])) ? $req['reassign'] : null;

  echo wp_delete_user($uid, $reassign);  // true | false
  die();
}




//00000000000000000000000000000000000000000000000000000000000000000000
// 投稿
//00000000000000000000000000000000000000000000000000000000000000000000
/** ***************************************
 * get_posts
 * @param Number author_id   - 投稿者ID
 * @param String post_type   - 投稿タイプ
 * @param String post_status - 投稿ステータス(draft|publish|private|trash and more)
 **************************************** */
add_action('wp_ajax_getPosts', 'getPosts');
add_action('wp_ajax_nopriv_getPosts', 'getPosts');
function getPosts() {
  $req       = $_REQUEST;

  $args = array(
    'posts_per_page'   => -1,
    'orderby'          => 'post_date',
  );

  if (isset($req['author_id'])) $args['author'] = $req['author_id'];
  $args['post_status'] = ($req['post_status']) ? $req['post_status'] : 'any';
  $args['post_type']   = ($req['post_type'])   ? $req['post_type'] : 'post';
  $args['order']       = ($req['order_desc'])  ? 'DESC' : 'ASC';

  $posts_array = get_posts($args);

  foreach ($posts_array as $post) {
    $post_id = $post->ID;

    // タクソノミー情報
    foreach (get_post_taxonomies($post_id) as $tax) {
      if ($tax !== 'category' and $tax !== 'post_tag') {
        $terms = wp_get_post_terms($post_id, $tax, ['fields' => 'ids']);
        $post->{$tax} = $terms;
      }
    }

    // サムネイル情報
    $thumbnail_id = get_post_thumbnail_id($post_id);
    if ($thumbnail_id) {
      $post->thumbnail = [
        'id' => $thumbnail_id,
        'url' => get_the_post_thumbnail_url($post_id),
      ];
    }

    // カスタムフィード
    $post->acf = get_fields($post_id);
    $post->id  = $post_id;
    unset($post->ID);

    $ret[] = $post;
  }

  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}


/** ***************************************
 * get_post
 * @param Number post_id    - 【条件付必須】投稿ID（スラッグを指定しない場合）
 * @param String slug       - 【条件付必須】スラッグ（投稿IDを指定しない場合）
 * @param String post_type  - 【条件付必須】投稿タイプ（投稿IDを指定しない場合）
 **************************************** */
add_action('wp_ajax_getPost', 'getPost');
add_action('wp_ajax_nopriv_getPost', 'getPost');
function getPost() {
  $req       = $_REQUEST;
  $post_id   = (isset($req['post_id']))   ? $req['post_id']   : null;
  $post_slug = (isset($req['slug']))      ? $req['slug']      : null;
  $post_type = (isset($req['post_type'])) ? $req['post_type'] : 'post';


  if (!$post_id) {
    $post_id = get_page_by_path($post_slug, "OBJECT", $post_type)->ID;
  }

  // オブジェクトベース
  $ret = get_post($post_id);

  // タクソノミー情報
  foreach (get_post_taxonomies($post_id) as $tax) {
    if ($tax !== 'category' and $tax !== 'post_tag') {
      $terms = wp_get_post_terms($post_id, $tax, ['fields' => 'ids']);
      $ret->{$tax} = $terms;
    }
  }

  // サムネイル情報
  $thumbnail_id = get_post_thumbnail_id($post_id);
  if ($thumbnail_id) {
    $ret->thumbnail = [
      'id' => $thumbnail_id,
      'url' => get_the_post_thumbnail_url($post_id),
    ];
  }

  // カスタムフィード
  $ret->acf = get_fields($post_id);
  $ret->id  = $post_id;
  unset($ret->ID);
  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}


/** ***************************************
 * upsert_post
 * @param String post_title   - 【必須】投稿タイトル
 * @param String post_type    - 【必須】投稿タイプ
 * @param Number author_id    - 【必須】投稿者ID
 * @param Number post_id      - 【条件必須】投稿ID（あれば更新）
 * @param String post_content - 投稿内容
 * @param Array  post_slug    - スラッグ
 * @param String post_status  - [default] publish | draft
 * @param Array  taxonomies   - タクソノミー
 * @param Array  acf          - カスタムフィード
 **************************************** */
add_action('wp_ajax_upsertPost', 'upsertPost');
add_action('wp_ajax_nopriv_upsertPost', 'upsertPost');
function upsertPost() {
  $rate = 0;
  $req      = $_REQUEST;
  $post_obj = array();
  if ($req['post_id'])      $post_obj['ID']           = $req['post_id'];      // 投稿ID（あれば更新）
  if ($req['post_slug'])    $post_obj['post_name']    = $req['post_slug'];    // スラッグ
  if ($req['post_title'])   $post_obj['post_title']   = $req['post_title'];   // タイトル
  if ($req['post_content']) $post_obj['post_content'] = $req['post_content']; // 投稿内容
  if ($req['post_date'])    $post_obj['post_date']    = $req['post_date'];    // 投稿日

  // print_r($req['taxonomies']);

  $post_obj['post_author'] = ($req['post_author']) ? $req['post_author'] : 1;              // 投稿者ID
  $post_obj['post_status'] = ($req['post_status']) ? $req['post_status'] : 'publish';      // [default] publish | draft
  $post_obj['post_type']   = ($req['post_type'])   ? $req['post_type']   : 'enterprises';  // 投稿タイプ

  $acf  = ($req['acf']) ? $req['acf'] : null; // カスタムフィード
  $taxs = $req['taxonomies'];    // タクソノミー



  try {
    // print_r($post_obj);
    // ------------------------------
    // 更新
    // - カスタムフィードを先に処理
    // ------------------------------
    if ($post_obj['ID']) {
      $post_id = $post_obj['ID'];
      // acfフィールド更新
      if ($acf) {
        foreach ($acf as $key => $val) {
          update_field($key, $val, $post_id);
        }
      }


      // 投稿基本データ
      $post_id = wp_insert_post($post_obj);
    }
    // ------------------------------
    // 新規作成
    // - カスタムフィードは後で処理
    // ------------------------------
    else {
      // 投稿基本データ
      $post_id = wp_insert_post($post_obj);

      // acfフィールド更新
      if ($acf) {
        foreach ($acf as $key => $val) {
          update_field($key, $val, $post_id);
        }
      }
    }

    // if($taxs) {
    //     foreach($taxs as $tax => $terms) {
    //         wp_set_object_terms( $post_id, $terms, $tax, $append );
    //     }
    // }

  } catch (Exception $e) {
    echo '保存エラー: ',  $e->getMessage(), "\n";
    exit;
  }

  // オブジェクトベース
  $ret = get_post($post_id);
  $ret->acf = get_fields($post_id);


  if ($taxs) {
    foreach ($taxs as $taxonomy => $terms) {
      if (is_array($terms)) {
        $newTaxs = array_map('intval', $terms);
        wp_set_object_terms($ret->ID, $newTaxs, $taxonomy);
      }
    }
  }

  // タクソノミー情報
  foreach (get_post_taxonomies($post_id) as $tax) {
    if ($tax !== 'category' and $tax !== 'post_tag') {
      $terms = wp_get_post_terms($post_id, $tax, ['fields' => 'ids']);
      $ret->{$tax} = $terms;
      if ($terms) $rate += 10;  // 1カテゴリーにつき10点(max 20pt)
    }
  }
  // サムネイル情報
  $thumbnail_id = get_post_thumbnail_id($post_id);
  if ($thumbnail_id) {
    $ret->thumbnail = [
      'id' => $thumbnail_id,
      'url' => get_the_post_thumbnail_url($post_id),
    ];
    $rate += 20;    // 画像登録で20点
  }


  if ($req['post_type'] == 'enterprises') {
    $postAcfFields = acf_get_fields('group_6139c86bd9b08'); // 事業所情報 ACFグループ
    $userAcfFields = acf_get_fields('group_614967bbadd6c'); // 基本情報   ACFグループ
    $allAcfFields = array_merge($postAcfFields, $userAcfFields);

    $postAcf = get_fields($post_id);
    $userAcf = get_fields("user_{$req['post_author']}");
    $acfFields = array_merge($postAcf, $userAcf);

    /** *************************************
     * ********** 除外フィールド ************
     * *************************************/
    $excludeFlds = [
      'ページテンプレート',   // 事業者投稿
      '事業所id',             // 事業者投稿
      '入力達成率',           // 事業者投稿
      '外部リンクを使用',     // 基本情報
      '求人規約承諾',         // 基本情報
      '緯度',                 // 基本情報
      '経度',                 // 基本情報
    ];
    /** ********************************** */

    $acfCount = count($acfFields) - 7;
    $acfPoint = round(60 / $acfCount, 1);

    foreach ($allAcfFields as $arr) {
      if (!in_array($arr['name'], $excludeFlds)) {
        if ($acfFields[$arr['name']]) $rate += $acfPoint;
      }
    }

    $rate = ($rate > 100) ? 100 : round($rate);
    update_field('入力達成率', $rate, $post_id);
    $ret->acf['入力達成率'] = $rate;
  }

  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}

/** ***************************************
 * update_post
 * @param Number post_id    - 【必須】投稿ID
 * @param String post_type  - 【必須】投稿タイプ
 * @param Array  post_title - 【必須】タイトル
 * @param Array  taxonomies - タクソノミー
 * @param Array  acf        - カスタムフィード
 * @param Boolean publish   - 「公開」に切り替え
 **************************************** */
add_action('wp_ajax_updatePost', 'updatePost');
function updatePost() {
  $req        = $_REQUEST;
  $post_id    = $req['post_id'];

  $post_obj = array();
  $post_obj['ID']           = $post_id;
  $post_obj['post_title']   = $req['post_title'];
  $post_obj['post_content'] = $req['post_content'];

  if (isset($req['taxonomies'])) $tax = $req['taxonomies'];
  if (isset($req['acf']))        $acf = $req['acf'];

  $publish = (isset($req['publish'])) ? $req['publish'] : false;

  try {
    // 投稿更新
    print_r($post_obj);
    $ret = wp_update_post($post_obj);

    // タクソノミー更新
    if (isset($tax)) {
      foreach ($tax as $slug => $terms) {
        wp_set_post_terms($post_id, $terms, $slug);
      }
    }

    // acfフィールド更新
    if ($acf) {
      foreach ($acf as $key => $val) {
        update_field($key, $val, $post_id);
      }
    }

    //  投稿ステータスを「公開」にする
    if ($publish) wp_publish_post($post_id);
  } catch (Exception $e) {
    echo '保存エラー: ',  $e->getMessage(), "\n";
  }

  echo $ret;
  die();
}


/** ***************************************
 * trash_post
 * @param Array  post_id  - 【必須】投稿ID
 **************************************** */
add_action('wp_ajax_updatePostStatus', 'updatePostStatus');
add_action('wp_ajax_nopriv_updatePostStatus', 'updatePostStatus');
function updatePostStatus() {
  $req     = $_REQUEST;
  $id      = $req['id'];
  $status  = $req['status'];

  $ret = wp_update_post(array(
    'ID' => $id,
    'post_status' => $status,
  ), true);
  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}


/** ***************************************
 * trash_post
 * @param Array  post_id  - 【必須】投稿ID
 **************************************** */
add_action('wp_ajax_trashPost', 'trashPost');
add_action('wp_ajax_nopriv_trashPost', 'trashPost');
function trashPost() {
  $req     = $_REQUEST;
  $post_id = $req['post_id'];

  $ret = wp_trash_post($post_id);
  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}

/** ***************************************
 * get_custom_field
 * @param String  post_type   - 【必須】投稿ID
 **************************************** */
add_action('wp_ajax_getMetaFields', 'getMetaFields');
add_action('wp_ajax_nopriv_getMetaFields', 'getMetaFields');
function getMetaField() {
  $req        = $_REQUEST;
  $post_type  = $req['post_type'];

  if ($post_type) {
    // $ret = 

    // echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  }
  die();
}



/** ***************************************
 * update_custom_field
 * @param Number  post_id   - 【必須】投稿ID
 * @param Array   fields    - 【必須】['<field name>' => '<value>']
 **************************************** */
add_action('wp_ajax_updateMetaField', 'updateMetaField');
add_action('wp_ajax_nopriv_updateMetaField', 'updateMetaField');
function updateMetaField() {
  $req        = $_REQUEST;
  $post_id    = $req['post_id'];
  $fields     = $req['fields'];

  $ret = array();
  foreach ($fields as $key => $val) {
    $bool = update_field($key, $val, $post_id);
    $ret[$key] = $bool;
  }

  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}





//00000000000000000000000000000000000000000000000000000000000000000000
// タクソノミー
//00000000000000000000000000000000000000000000000000000000000000000000
/** ***************************************
 * タクソノミー取得
 *************************************** */
add_action('wp_ajax_getTaxonomyTerms', 'getTaxonomyTerms');
add_action('wp_ajax_nopriv_getTaxonomyTerms', 'getTaxonomyTerms');
function getTaxonomyTerms() {
  $req      = $_REQUEST;
  $taxonomy = $req['taxonomy'];

  if (is_array($taxonomy)) {
    foreach ($taxonomy as $tax) {
      $ret[$tax] = categoryObj($tax);
    }
  } else {
    $ret = categoryObj($taxonomy);
  }

  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}
/** ***************************************
 * タクソノミー取得（オプションなし）
 *************************************** */
add_action('wp_ajax_getTaxonomyTermsNoOption', 'getTaxonomyTermsNoOption');
add_action('wp_ajax_nopriv_getTaxonomyTermsNoOption', 'getTaxonomyTermsNoOption');
function getTaxonomyTermsNoOption() {
  $req      = $_REQUEST;
  $taxonomy = $req['taxonomy'];

  $ret = get_terms($taxonomy, array(
    'hide_empty' => false,
    'orderby'    => 'slug',
    'order'      => 'ASC'
  ));

  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}


/** ***************************************
 * タクソノミーターム取得
 * @param string taxonomy        - 【必須】タクソノミースラッグ
 * @param string key             - 【必須】検索ターゲットのキー('id'|'slug'|'name')
 * @param array|string|int value - 【必須】タクソノミースラッグ
 *************************************** */
add_action('wp_ajax_getTaxonomyTermObject', 'getTaxonomyTermObject');
add_action('wp_ajax_nopriv_getTaxonomyTermObject', 'getTaxonomyTermObject');
function getTaxonomyTermObject() {
  $req      = $_REQUEST;
  $taxonomy = $req['taxonomy'];
  $key      = $req['key'];
  $value    = $req['value'];

  if (is_array($value)) {
    foreach ($value as $val) {
      $obj = get_term_by($key, $value, $taxonomy, 'ARRAY_A');
      if ($obj) {
        $ret[] = $obj;
      }
    }
  } else {
    $ret = get_term_by($key, $value, $taxonomy, 'ARRAY_A');
  }

  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}



/** ***************************************
 * タグ追加
 *************************************** */
add_action('wp_ajax_addTag', 'addTag');
function addTag() {
  $req      = $_REQUEST;
  $term     = $req['term'];
  $termName = $term['name'];
  $termSlug = $term['slug'];
  $taxonomy = $req['taxonomy'];

  $ret = wp_insert_term($termName, $taxonomy, ['slug' => $termSlug]);

  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}


/** ***************************************
 * タグ取得
 *************************************** */
add_action('wp_ajax_getTags', 'getTags');
function getTags() {
  $req      = $_REQUEST;
  $taxonomy = $req['taxonomy'];
  $termIds  = $req['termIds'];

  if (is_array($termIds)) {
    foreach ($termIds as $id) {
      $ret[] = get_term($id, $taxonomy);
    }
  } else {
    $ret = get_term($termIds, $taxonomy);
  }

  echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  die();
}


/** ***************************************
 * 画像ダウンロード
 * @param Number media_id - メディアID
 *************************************** */
add_action('wp_ajax_downloadMedia', 'downloadMedia');
add_action('wp_ajax_nopriv_downloadMedia', 'downloadMedia');
function downloadMedia() {
  $req      = $_REQUEST;
  $media_id = $req['id'];

  if ($media_id) {
    $file_info  = new finfo(FILEINFO_MIME_TYPE);
    $file_path  = get_attached_file($media_id);
    if (!$file_path) return;
    $file_name  = array_reverse(explode('/', $file_path))[0];
    $file       = file_get_contents($file_path);
		$mime_type  = $file_info->buffer($file);

    echo json_encode([
      'data'      => base64_encode($file),
      'name'      => $file_name,
      'mimeType'  => $mime_type,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  }
  die();
}


/** ***************************************
 * 画像アップロード
 * @param Object $_FILES - 【必須】ファイルオブジェクト
 * @param Number post_id - 投稿ID
 *************************************** */
add_action('wp_ajax_uploadMedia', 'uploadMedia');
add_action('wp_ajax_nopriv_uploadMedia', 'uploadMedia');
function uploadMedia() {
  $req      = $_REQUEST;
  $post_id  = $req['post_id'];
  $file_key = array_keys($_FILES)[0];

  switch ($_FILES[$file_key]['type']) {
    case 'image/gif':
      $type = 'gif';
      break;
    case 'image/png':
      $type = 'png';
      break;
    default:
      $type = 'jpg';
      break;
  }

  // 画像ファイル名変更
  $_FILES[$file_key]['name'] = hash('sha256', $_FILES[$file_key]['name']) . '.' . $type;

  try {
    // 画像アップロード
    $id  = media_handle_upload($file_key, 0);
    $url = wp_get_attachment_url($id);

    if (is_numeric($id)) {
      // 投稿に紐付け
      set_post_thumbnail($post_id, $id);
    }

    $ret = ['id'  => $id, 'url' => $url];
    echo json_encode($ret, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  } catch (Exception $e) {
    echo '保存エラー: ',  $e->getMessage(), "\n";
    exit;
  }
  die();
}


/** ***************************************
 * 画像削除
 * @param Number media_id - 【必須】投稿ID
 * @param Number post_id  - 投稿ID
 *************************************** */
add_action('wp_ajax_removeMedia', 'removeMedia');
add_action('wp_ajax_nopriv_removeMedia', 'removeMedia');
function removeMedia() {
  $req      = $_REQUEST;
  $post_id  = $req['post_id'];
  $media_id = $req['media_id'];

  try {
    if ($post_id) {
      // 投稿に紐づくサムネイルを削除
      delete_post_thumbnail($post_id);
    }

    // 「メディア」からファイルを完全に削除
    wp_delete_attachment($media_id, true);

    echo json_encode(['message' => 'ファイルが削除されました。'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  } catch (Exception $e) {
    echo '削除エラー: ',  $e->getMessage(), "\n";
    exit;
  }
  die();
}

/** ***************************************
 * メール送信
 * @param Array|String to               - 送信先
 * @param Array        headers          - ヘッダー("From" "Bcc" "Cc" and more)
 * @param String       subject          - 件名
 * @param String       message_customer - （顧客用）メッセージ本文
 * @param String       message_owner    - （自社用）メッセージ本文
 *************************************** */
add_action('wp_ajax_sendEmail', 'sendEmail');
function sendEmail() {
  $req              = $_REQUEST;
  $to               = $req['to'];
  $headers          = $req['headers'];
  $subject          = $req['subject'];
  $message_customer = $req['message_customer'];
  $message_owner    = $req['message_owner'];

  if ($to and $subject) {
    // 顧客用メール
    if ($message_customer) wp_mail($to, $subject, $message_customer, implode("\r\n", $headers));

    // 自社用メール
    if ($message_owner) wp_mail('info@digital-town.jp', $subject, $message_owner);
  }
  die();
}





/** **********************************************************
 * ユーザー作成時、作成したユーザーと管理者へメール送信
 * ******************************************************** */
function userCreate_sendEmail($user_id, $userdata) {
  $data = ['id' => $user_id, 'data' => $userdata];
  $jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
  echo $jsonData; // jsonデータをフロントに吐き出す
  if ($user_id['errors']) return;
  file_put_contents(dirname(__DIR__) . '/log/user_regist.log', $jsonData, FILE_APPEND);

  $email      = $userdata['username'];
  $username   = $userdata['username'];
  $password   = $userdata['password'];
  $enterprise = $userdata['meta']['display_name'];


  // ---------------------------------------------
  // 管理者宛に登録通知メールを送る
  // ---------------------------------------------
  $subject   = '【デジタル町一丁目】みんなのまちサイト - 新規会員登録の通知';
  $message = <<<EOD
新規ユーザーが作成されました。

事業所名：{$enterprise}
ユーザー名：{$username}
パスワード：{$password}
EOD;
  // $headers[] = "Bcc: info@digital-town.jp"; // wp_mail第4引数
  wp_mail(get_option('admin_email'), $subject, $message);

  // ---------------------------------------------
  //ユーザー宛に登録通知メールを送る
  // ---------------------------------------------
  $subject = '【デジタル町一丁目】みんなのまちサイト - 登録のご案内';
  $message = <<<EOD
{$enterprise}
ご担当者様

アニバーサリーコンシェル㈱です。
このたびは、 「デジタル町一丁目（みんなのまちサイト/とさジョブ）」
にお申込みいただき、まことにありがとうございます。

【みんなのまちサイト】の貴社のマイページが編集していただけるようになりました。
下記のURLからログインして確認/編集していただけます。


　URL: https://minna.digital-town.jp/login
　────────────────────
　ログイン名: {$username}

　パスワード: {$password}
　────────────────────

　１．上記URLにアクセスし、
　　　ログイン名/パスワードをご入力のうえ
　　　「ログイン」ボタンを押す

　２．貴社のMyページが開きます。
　　　住民の方にアピールしたい内容など、
　　　画像や文章を自由にご入力ください。

※ とさジョブでの求人掲載は【求人情報】ページからご入力ください。
　 他に【クーポン】【イベント】ページがございます。（掲載はいずれも、ずっと無料です）

◆実際の掲載イメージ・みんなのまちサイト◆
https://minna.digital-town.jp/enterprises

他にも、営業に有効活用していただける有料機能
（スカウト配信/ターゲット指定配信など）がございます。
詳しくは、以下ホームページ内の「事業所向け」タブをクリックしてご確認ください。
https://digital-town.jp/

なお、このメールアドレスは送信専用のためご返信いただけません。
ご不明な点等ございましたら、下記までお問合せいただきますようお願い申し上げます。


────────────────────────────────────────────────────

アニバーサリーコンシェル株式会社
デジタル町一丁目 / とさジョブ

メール：info@digital-town.jp
電話  ：088-832-1221 (平日10:00～17:30)

────────────────────────────────────────────────────
EOD;
  // $headers[] = "Bcc: info@digital-town.jp"; // wp_mail第4引数
  wp_mail($email, $subject, $message);
  exit;
}
