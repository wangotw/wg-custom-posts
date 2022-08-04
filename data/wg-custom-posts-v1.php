<?php
/*
Plugin Name: WG Custom Posts
Plugin URI: https://www.iwangoweb.com/
Description: 客製化版型Query Posts，包裝成帶有參數之短碼。
Version: 1.1
Author: 玩構網路
Author URI: https://www.iwangoweb.com/about/
*/

/*
function add_css_cdn(){
    echo "
	<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css' integrity='sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l' crossorigin='anonymous'>	
    ";
    }
    add_action('wp_head', 'add_css_cdn');

function add_js_cdn(){
    echo "
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js' integrity='sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns' crossorigin='anonymous'></script>
    ";
    }
    add_action('wp_footer', 'add_js_cdn');
*/

//一共3個function  分別用途：1.(後端)使用者輸入name轉成slug 2.(後端)轉換後的slug進行搜尋商家 3.(前端)顯示商家資訊

//轉換使用者輸入 name->slug
function findSlugName_function($user_input) 
{
	//引述處理
	$userInput = shortcode_atts(array(
		'name' =>'',
	), $user_input);
	
	$term = [[]];
	
	//搜尋條件
	$query_string = array(
		'post_type' => 'loan',
		'post_per_page' => -1,
		'advertise_loan' => '',
    );
	
	echo "使用者輸入：".$userInput['name']."<br/>";
	
	$queryCategory = new WP_Query($query_string);
	
  	if($queryCategory ->have_posts()) 
	{
		$i=0;
		while($queryCategory ->have_posts())
		{		
			$queryCategory ->the_post();
			$cur_id = get_the_ID(); //取得id
			$terms = get_the_terms($cur_id,'advertise_loan');
			//var_dump($terms);

			//找到所有post的slug與name存起來
			foreach($terms as $value)
			{
				$term[$i]['name'] = $value->name;
				$term[$i]["slug"] = $value->slug;
				echo $i.$term[$i]['name'];
				$i++;
			}
		}
		
		//開始比對輸入資料與陣列
		
		$i=0;
		while($term[$i]['name'] != null)
		{
			
			if($term[$i]['name'] == $userInput['name']) 
			{
				echo "轉換成slug：".$term[$i]['slug'];
				cptQuery($term[$i]['slug']); //找到相符的name 把對應的slug傳入function
				break;
			}
			else
			{
				$i++;
			}
			
		}
	}
	
	
}

/*搜尋文章分類下的店家資訊*/
function cptQuery($slug)
{
	
	$content = [[]]; //放內容標題、圖片網址、連結
	//搜尋字串
	$query_string = array(
		'post_type' => 'loan', //cpt slug的名稱
		'advertise_loan' =>$slug,
		'post_per_page' => -1, //所有搜尋的post
	);
	
	$queryCategory = new WP_Query($query_string); //搜尋分類內容
	
	$i=0;
	//存在post時執行
	if($queryCategory->have_posts())
	{
		while($queryCategory -> have_posts())
		{
			$queryCategory -> the_post(); //指向下一篇
			$content[$i]['featuredImg'] = get_the_post_thumbnail_url(get_the_ID(),'full');  //取得特色圖片
			$content[$i]['title'] = get_the_title(); //取得標題
			$content[$i]['link'] = get_the_permalink(); //取得網址
			//echo "title:".$content[$i]['title']."<br/>"." link：".$content[$i]['link']."<br/>"; 
			$i++;
		}
	}
	showContent($content);
	
}

/*前端顯示部分*/
function showContent($content)
{
	//echo "title:".$content[0]['title']."<br/>"." link：".$content[0]['link']."<br/>"; 
	$i=0;
	foreach($content as $value)
	{
		echo "<p>".$content[$i]['title']."</p>";
		//echo "<a href='".$content[$i]['link']."'><img src ='".$content[$i]['featuredImg']."'></a>";
		echo "<a href='".$content[$i]['link']."'><img src ='".$content[$i]['featuredImg']."' style='width:33%; float:left;'></a>";
		$i++;

		
	}
		
	
}


add_shortcode("WGcpt", "findSlugName_function"); //註冊shortcode

?>