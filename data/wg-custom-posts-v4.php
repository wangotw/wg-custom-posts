<?php

//安全貸CPT Query開發
//v4 20220324 新增CSS前端整合
//一共5個function  分別用途：1.主程式整合 2.(後端)使用者輸入name轉成slug 3.(後端)轉換後的slug進行搜尋商家 4.(前端)顯示商家資訊 5.引入外部CSS檔
/*整合用主程式*/
function main_function($userInput)
{
	$cptSlug = 'company'; //想搜尋cpt的slug
	$cptTaxSlug = 'loan'; //想搜尋cpt內taxonomy的slug
	$post_per_page = -1; //文章呈現數量
	$taxSlug = findTaxSlug($userInput,$cptSlug,$cptTaxSlug); //轉換使用者輸入 
	$content = cptQuery($cptSlug,$taxSlug,$cptTaxSlug,$post_per_page); //搜尋文章分類下的店家資訊	
	if($taxSlug != null)
	{
		$html = showContent($content); //前端顯示
		return $html;
	}
	else
	{
		return "尚無資料";
	}
	
}

/*轉換使用者輸入 name->slug*/
function findTaxSlug($userInput,$cptSlug,$cptTaxSlug) 
{
	//引數處理
	$taxName = shortcode_atts(array(
		'tax' =>'',
	), $userInput);
	$term = [[]];
	//搜尋條件
	$query_string = array(
		'post_type' => $cptSlug,
		'post_per_page' => -1,
		 $cptTaxSlug => '',
    );
	
	//echo "使用者輸入：".$taxName['name']."<br/>";
	$queryCategory = new WP_Query($query_string);
  	if($queryCategory ->have_posts()) 
	{
		$i=0;
		while($queryCategory ->have_posts())
		{		
			$queryCategory ->the_post(); //指向下篇
			$cur_id = get_the_ID(); //取得id
			$terms = get_the_terms($cur_id,$cptTaxSlug); //搜尋附加至term的相關資訊
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
		foreach($term as $value)
		{
			//echo "||".$term[$i]['name'];
			if($value['name'] == $taxName['tax']) 
			{
				//echo "轉換成slug：".$value['slug'];
				return $value['slug']; //找到相符的name 把對應的slug傳入function
			}
			
		}
	}
	else
	return 0;
}
/*搜尋文章分類下的店家資訊*/
function cptQuery($cptSlug,$taxSlug,$cptTaxSlug,$post_per_page)
{
	$content = [[]]; //放內容標題、圖片網址、連結
	//搜尋字串
	/*
	$query_string = array(
		'post_type' => $cptSlug, //cpt slug的名稱
		'company' => $taxSlug, //tax slug名稱
		'post_per_page' => $post_per_page, //搜尋篇數 全部 = -1
	);*/
	$query_string = array(
		'post_type' => $cptSlug, //cpt slug的名稱
		$cptTaxSlug => $taxSlug, //tax slug名稱
		'post_per_page' => $post_per_page,
	);
	
	$queryCategory = new WP_Query($query_string); //搜尋分類內容
	$i=0;
	//存在post時執行
	if($queryCategory->have_posts())
	{
		$check = 1;
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
	echo "<link rel='stylesheet' type='text/css' media='screen' href='assets/css/style_aspect.css'>"; //標頭檔外部引入自訂的css檔
	$show = "<div class='grid-container'> "; //外層包全部的店家
	foreach($content as $value)
	{
		$show .= "<div class='grid-item'>";//包單一個商家的圖片與文字

		$show .="<div class='grid-item-img'>";
		$show .= "<a href='".$value['link']."'>";
		$show .= "<img src ='".$value['featuredImg']."''>"; //圖片
		$show .="</a>";
		$show .="</div>";
		$show .="<div class='grid-item-text'>";
		$show .= "<a href='".$value['link']."'>";
		$show .= "<p>".$value['title']."</p>"; //文字
		$show .="</a>";
		$show .="</div>";
		$show .="</div>";
	
	}

	$show .="</div>";
	return $show;
}

/*加入外部CSS檔*/
function wg_custom_posts_css()
{
	wp_enqueue_style( 'css_style', plugins_url( 'wg-custom-posts/assets/css/wg-cpt-grid.css'));
	wp_enqueue_style( 'css_style' );
}

add_action('wp_footer', 'wg_custom_posts_css');
add_shortcode("loan-co", "main_function"); //註冊shortcode
?>