<?php
/*
Plugin Name: WG Custom Posts
Plugin URI: https://www.iwangoweb.com/
Description: 客製化版型Query Posts，包裝成帶有參數之短碼。
Version: 1.2.4
Author: 玩構網路
Author URI: https://www.iwangoweb.com/about/
*/

//安全貸CPT Query開發
//v0.4 20220324 新增CSS前端整合
//v0.5 20220408 新增使用者輸入參數name，tax用於完全比對，name用於服務項目模糊比對
//v0.6 20220419 分割不同功能程式至其他檔案，微調程式撰寫風格
//v0.7 20220819 修正 cptQuery.php : 新增加入資料比對，取得不重複資料
//v0.8 20220926 修改模糊比對顯示邏輯，修改為按照地區顯示全台縣市金融機構。
//v1.2.1 20221006 修改CSS樣式
//v1.2.2 20221101 屏東市 -> 屏東縣
//v1.2.3 20221108 Query參數 post_per_page -> posts_per_page
//v1.2.4 20221229 修改模糊比對時，儲存陣列邏輯 : index -> push
//一共5個function  分別用途：1.主程式整合 2.(後端)使用者輸入name轉成slug 3.(後端)轉換後的slug進行搜尋商家 4.(前端)顯示商家資訊 5.引入外部CSS檔
/*整合用主程式*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

//可控制引數
$cptSlug = 'company'; //想搜尋cpt的slug
$cptTaxSlug = 'loan'; //想搜尋cpt內taxonomy的slug
$post_per_page = -1; //文章呈現數量

function main_function( $userInput ) {

    /*外部引入檔案*/
    include_once plugin_dir_path( __FILE__ ) . 'includes/cptQuery.php';
    include_once plugin_dir_path( __FILE__ ) . 'includes/findTaxSlug.php';
    include_once plugin_dir_path( __FILE__ ) . 'includes/getNameContent.php';
    include_once plugin_dir_path( __FILE__ ) . 'includes/getTaxContent.php';

    //引用全域變數
    global $cptSlug; //想搜尋cpt的slug
    global $cptTaxSlug; //想搜尋cpt內taxonomy的slug
    global $post_per_page; //文章呈現數量

    //使用者輸入引數處理
    $taxName = shortcode_atts( array(
        'tax' => '',
        'name' => '',
    ),  $userInput );

    //判斷使用者輸入為何，進入不同模式
    if ( $taxName['name'] !== '' ) {
        $mode = "part"; //存在使用者輸入name 比對taxslug底下是否有部分相符的字串
    }
    elseif ( $taxName['tax'] !== '' ) {
        $mode = "full"; //存在使用者輸入tax 轉換使用者輸入
    }
    else {
        $mode = "null";
    }

    //依據使用者輸入 分配至不同流程
    switch ( $mode ) {
        case "null":
            return "請輸入tax或name進行搜尋";
            break;
        //模糊比對
        case "part":
            $taxSlug = findTaxSlug($taxName['name'], $cptSlug, $cptTaxSlug, $mode);//比對CPT底下是否有部分字串符合的Taxonomy 透過轉換將name轉成slug
            $content = cptQuery($cptSlug, $taxSlug, $cptTaxSlug, $post_per_page, $mode);//進行商家搜尋，不同城市存入不同的陣列中
            $html = getNameContent($content);//前端顯示
            return $html;
            break;
        //完全比對
        case "full":
            $taxSlug = findTaxSlug($taxName['tax'], $cptSlug, $cptTaxSlug, $mode); //轉換使用者輸入
            $content = cptQuery($cptSlug, $taxSlug, $cptTaxSlug, $post_per_page, $mode); //搜尋文章分類下的店家資訊
            $html = getTaxContent($content); //前端顯示
            return $html;
            break;
        default:
            return;
    }
}
add_shortcode( "loan-co", "main_function" ); //註冊shortcode

/*加入css檔*/
function wg_custom_posts_css() {
    echo "<link rel=stylesheet type='text/css' href='" . plugin_dir_url( __FILE__ ) . "assets/css/wg-cpt-grid.css'>";
}
add_action( 'wp_head', 'wg_custom_posts_css' );

/*加入js檔*/
function wg_custom_posts_js() {
    echo "<script type='text/javascript' src='"  . plugin_dir_url( __FILE__ ) . "assets/js/tabs.js'></script>";
}
add_action( 'wp_footer', 'wg_custom_posts_js' );
