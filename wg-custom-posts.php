<?php
/*
Plugin Name: WG Custom Posts
Plugin URI: https://www.iwangoweb.com/
Description: 客製化版型Query Posts，包裝成帶有參數之短碼。
Version: 1.1
Author: 玩構網路
Author URI: https://www.iwangoweb.com/about/
*/

//安全貸CPT Query開發
//v4 20220324 新增CSS前端整合
//v5 20220408 新增使用者輸入參數name，tax用於完全比對，name用於服務項目模糊比對
//v6 20220419 分割不同功能程式至其他檔案，微調程式撰寫風格
//一共5個function  分別用途：1.主程式整合 2.(後端)使用者輸入name轉成slug 3.(後端)轉換後的slug進行搜尋商家 4.(前端)顯示商家資訊 5.引入外部CSS檔
/*整合用主程式*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function main_function( $userInput ) {

	/*外部引入檔案*/
	include_once plugin_dir_path( __FILE__ ) . 'includes/cptQuery.php';
	include_once plugin_dir_path( __FILE__ ) . 'includes/findTaxSlug.php';
	include_once plugin_dir_path( __FILE__ ) . 'includes/getNameContent.php';
	include_once plugin_dir_path( __FILE__ ) . 'includes/getTaxContent.php';
	
	//可控制引數
	$cptSlug = 'company'; //想搜尋cpt的slug
	$cptTaxSlug = 'loan'; //想搜尋cpt內taxonomy的slug
	$post_per_page = -1; //文章呈現數量

	//使用者輸入引數處理
	$taxName = shortcode_atts( array(
		'tax' => '', 
		'name' => '', 
	),  $userInput );

	//判斷使用者輸入為何，進入不同模式
	if ( $taxName['name'] !== '' ) {
		$mode = 1; //存在使用者輸入name 比對taxslug底下是否有部分相符的字串
	}
	elseif ( $taxName['tax'] !== '' ) {
		$mode = 2; //存在使用者輸入tax 轉換使用者輸入
	}
	else {
		$mode = 0;
	}

	//依據使用者輸入 分配至不同流程
	switch ( $mode ) {
		case 0:
			return "請輸入tax或name進行搜尋";
			break;
		//模糊比對
		case 1:
			$taxSlug = findTaxSlug($taxName['name'], $cptSlug, $cptTaxSlug, $mode);//比對CPT底下是否有部分字串符合的Taxonomy 透過轉換將name轉成slug
			$content = cptQuery($cptSlug, $taxSlug, $cptTaxSlug, $post_per_page, $mode);//進行商家搜尋，不同城市存入不同的陣列中
			$html = getNameContent($content);//前端顯示
			return $html;
			break;
		//完全比對
		case 2:
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
	echo "<link rel=stylesheet type='text/css' href='" . plugin_dir_url( __FILE__ ) . "assets/css/wg-cpt-grid_beta.css'>";
}
add_action( 'wp_head', 'wg_custom_posts_css' );

/*加入js檔*/
function wg_custom_posts_js() {
	echo "<script type='text/javascript' src='"  . plugin_dir_url( __FILE__ ) . "assets/js/tabs.js'>";
}
add_action( 'wp_footer', 'wg_custom_posts_js' );
?>