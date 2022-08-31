<?php
/*****************************************************************************/
/*  kintoneｱﾌﾟﾘ初期定義ファイル                               (Version 1.00) */
/*                                                                           */
/*   本ファイル内で定義されたものは各サイトごとに異なるものです              */
/*                                                                           */
/*                                    Copyright(C)2013 TIME CONCIER Co.,Ltd. */
/*****************************************************************************/
header("Access-Control-Allow-Origin: *");
header("Content-Type:text/html;charset=utf-8");
mb_language("Japanese");

// パスフレーズファイル
$pp = file_get_contents(__DIR__."/pp.key");

if (!defined("DEFKINTONECONF_INC")) {
    // ２重インクルード防止
    define( "DEFKINTONECONF_INC", true);

    ///////////////////////////////////////////////////////////////////////////////
    // 初期設定
    ///////////////////////////////////////////////////////////////////////////////
    define( "PASSPHRASE", $pp);

    define( "FORLINE_URL", "https://timeconcier.jp/forline");

	///////////////////////////////////////////////////////////////////////////////
	// MySQL設定
	///////////////////////////////////////////////////////////////////////////////
    define( 'DB_HOST', 'mysql8022.xserver.jp' );
    define( 'DB_NAME', 'tc2_digitown' );
    define( 'DB_USER', 'tc2_digitown' );
    define( 'DB_PASS', 'digitown2021' );


	///////////////////////////////////////////////////////////////////////////////
	// WORDPRESS「みんなのサイト」
	///////////////////////////////////////////////////////////////////////////////
    define( "SITE_DOMAIN", "minna.digital-town.jp");
    define( "ADMIN_ID"   , "admin");
    define( "ADMIN_PW"   , "A306 zJZC xubH HueR BbA6 FBD2");

    ///////////////////////////////////////////////////////////////////////////////
    // API Key
    ///////////////////////////////////////////////////////////////////////////////
    define( "GMAP_API_KEY", "AIzaSyBChf0zPlcih_TnU9xA502jVqVCJ072mCQ" );
    define( "BMAP_API_KEY", "ArurXeKTR7HWn6a9VtYiPeRkJibOj4PRhHzfyJe7ZYXsaWM0GIvPbnJTf90fTDZN" );

    ///////////////////////////////////////////////////////////////////////////////
    // グローバル変数
    ///////////////////////////////////////////////////////////////////////////////


    if(isset($_REQUEST['getConfig'])){
        // 設定情報連想配列
        $config = [
            'wp' => [
                'minna_no_site' => [
                    'domain'   => SITE_DOMAIN,
                    'admin_id' => ADMIN_ID,
                    'admin_pw' => ADMIN_PW,
                ]
            ],
            'mysql' => [
                'url'  => FORLINE_URL.'/tccom/tcLibMySQL.php',
                'auth' => [
                    'host' => DB_HOST,
                    'name' => DB_NAME,
                    'user' => DB_USER,
                    'pass' => DB_PASS,
                ]
            ],
            'api_key' => [
                'g_map' => GMAP_API_KEY,
                'b_map' => BMAP_API_KEY,
            ],
            'pp' => PASSPHRASE
        ];

        // 暗号化
        echo cryptoOpenSSLencdec(json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), PASSPHRASE, 'enc'). '-'. PASSPHRASE;
    }

}



/** ****************************************************************
 * CryptoJS用
 * 文字列暗号化・復号化(AES-256-CBC)
 * @param mixed $value - 暗号化したい値（文字列・配列または連想配列）
 * @param string $passphrase - パスフレーズ
 * @param string $mode - 'enc':暗号化 | 'dec': 復号化
 * @return array - [暗号化データ, 初期化ベクトル, ソルト化したパスフレーズ]
 **************************************************************** */
function cryptoOpenSSLencdec($value, $passphrase, $mode = 'enc'){
    // ---------------------------
    // 復号化
    // ---------------------------
    if($mode == 'dec'){
        // 暗号化データを分割
        $enc_data = explode('-', $value);

        $ct = base64_decode($enc_data[0]);  // base64方式で暗号化データをデコード
        $iv  = hex2bin($enc_data[1]);       // 初期ベクトル値の16進数をバイナリデータに変換
        $salt = hex2bin($enc_data[2]);      // ソルト値の16進数をバイナリデータに変換


        // パスフレーズとソルト値を連結
        $concatedPassphrase = $passphrase . $salt;

        $md5 = array();
        // パス+ソルト値をハッシュ化し、$md5[]に挿入
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }

        // ハッシュ化されたデータの1～33文字目をキーとする
        $key = substr($result, 0, 32);
        $data = openssl_decrypt(
            $ct,                // 暗号化された値
            'aes-256-cbc',      // 暗号化方式
            $key,               // キー
            OPENSSL_RAW_DATA,   // オプション（OPENSSL_RAW_DATA or OPENSSL_ZERO_PADDING）
            $iv                 // 初期化ベクトル
        );
    }

    // ---------------------------
    // 暗号化
    // ---------------------------
    else {
        // ソルト値生成
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';

        // パスフレーズをソルト化
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }

        // ソルト化されたデータの1～33文字目をキーとする
        $key = substr($salted, 0, 32);

        // ソルト化されたデータの33～48文字目を初期化ベクトルとする
        $iv  = substr($salted, 32, 16);

        // 値($plain_text)を暗号化
        $encrypted_data = openssl_encrypt(
            $value,        // 暗号化する値
            'aes-256-cbc',      // 暗号化方式
            $key,               // キー
            OPENSSL_RAW_DATA,   // オプション（OPENSSL_RAW_DATA or OPENSSL_ZERO_PADDING）
            $iv                 // 初期化ベクトル
        );

        // 暗号化データ、初期ベクトル、ソルト化
        $data = [
            base64_encode($encrypted_data), // base64方式で暗号化データをエンコード
            bin2hex($iv),   // 初期ベクトル値のバイナリデータを16進数に変換
            bin2hex($salt)  // ソルト値のバイナリデータを16進数に変換
        ];

        $data = implode('-', $data);
    }

    return $data;
}