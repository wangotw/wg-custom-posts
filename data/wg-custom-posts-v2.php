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
//一共4個function  分別用途：1.主程式整合 2.(後端)使用者輸入name轉成slug 3.(後端)轉換後的slug進行搜尋商家 4.(前端)顯示商家資訊


/*整合用主程式*/
function main_function($userInput)
{
	$cptSlug = 'loan'; //想搜尋cpt的slug
	$cptTaxSlug = 'advertise_loan'; //想搜尋cpt內taxonomy的slug
	$post_per_page = -1;

	$taxSlug = findSlugName($userInput,$cptSlug,$cptTaxSlug); //轉換使用者輸入 
	$content = cptQuery($cptSlug,$taxSlug,$post_per_page); //搜尋文章分類下的店家資訊
	$show = showContent($content); //前端顯示
	return $show;
}


/*轉換使用者輸入 name->slug*/
function findSlugName($userInput,$cptSlug,$cptTaxSlug) 
{
	//引數處理
	$taxName = shortcode_atts(array(
		'name' =>'',
	), $userInput);

	$term = [[]];
	
	//搜尋條件
	$query_string = array(
		'post_type' => $cptSlug,
		'post_per_page' => -1,
		 $cptTaxSlug => '',
    );
	
	echo "使用者輸入：".$taxName['name']."<br/>";
	
	$queryCategory = new WP_Query($query_string);
	
  	if($queryCategory ->have_posts()) 
	{

		$i=0;
		while($queryCategory ->have_posts())
		{		
			$queryCategory ->the_post(); //指向下篇
			$cur_id = get_the_ID(); //取得id
			$terms = get_the_terms($cur_id,'advertise_loan'); //搜尋附加至term的相關資訊
			//var_dump($terms);
			//找到所有post的slug與name存起來
			foreach($terms as $value)
			{
				$term[$i]['name'] = $value->name;
				$term[$i]["slug"] = $value->slug;
				$i++;
			}
			
		}
		
		//開始比對輸入資料與陣列
		
		$i=0;
		foreach($term as $value=>$x)
		{
			if($term[$i]['name'] == $taxName['name']) 
			{
				echo "轉換成slug：".$term[$i]['slug'];
				return $term[$i]['slug']; //找到相符的name 把對應的slug傳入function
			}
			else
			{
				$i++;
			}	
		}
	}
	return 0;
}

/*搜尋文章分類下的店家資訊*/
function cptQuery($cptSlug,$taxSlug,$post_per_page)
{
	
	$content = [[]]; //放內容標題、圖片網址、連結
	//搜尋字串
	$query_string = array(
		'post_type' => $cptSlug, //cpt slug的名稱
		'advertise_loan' => $taxSlug, //tax slug名稱
		'post_per_page' => $post_per_page, //搜尋篇數 全部 = -1
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
			$i++;
		}
	}
	return $content;
	
}

/*前端顯示部分*/
function showContent($content)
{
	//echo "title:".$content[0]['title']."<br/>"." link：".$content[0]['link']."<br/>"; 
	$i=0;
	$show = "";
	foreach($content as $value)
	{
		$show .= "<p>".$content[$i]['title']."</p>"."<a href='".$content[$i]['link']."'><img src ='".$content[$i]['featuredImg']."' style='width:33%; float:left;'></a>";
		//echo "<p>".$content[$i]['title']."</p>";
		//echo "<a href='".$content[$i]['link']."'><img src ='".$content[$i]['featuredImg']."'></a>";
		//echo "<a href='".$content[$i]['link']."'><img src ='".$content[$i]['featuredImg']."' style='width:33%; float:left;'></a>";
		$i++;

	}
	return $show;
	
}

add_shortcode("WGcpt", "main_function"); //註冊shortcode

?>