<?php

//安全貸CPT Query開發
//v3加入css 並修改function加入引數，增加彈性
//一共4個function  分別用途：1.主程式整合 2.(後端)使用者輸入name轉成slug 3.(後端)轉換後的slug進行搜尋商家 4.(前端)顯示商家資訊

/*整合用主程式*/
function main_function($userInput)
{
	$cptSlug = 'company'; //想搜尋cpt的slug
	$cptTaxSlug = 'loan'; //想搜尋cpt內taxonomy的slug
	$post_per_page = -1; //文章呈現數量
	
	$check = 0;//測試開關

	$taxSlug = findSlugName($userInput,$cptSlug,$cptTaxSlug); //轉換使用者輸入 
	$content = cptQuery($cptSlug,$taxSlug,$cptTaxSlug,$post_per_page); //搜尋文章分類下的店家資訊	
	if($check == 0)
	{
		return "無任何資訊";
	}
	else
	{
		$show = showContent($content); //前端顯示
		return $show;

	}
	
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
			if($value['name'] == $taxName['name']) 
			{
				echo "轉換成slug：".$value['slug'];
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
	//echo "title:".$content[0]['title']."<br/>"." link：".$content[0]['link']."<br/>"; 
	
	$show = "";
	foreach($content as $value)
	{
		
		$show .= "<p>".$value['title']."</p>"."<a href='"
		.$value['link']."'><img src ='".$value['featuredImg']
		."''></a>";

	}
	return $show;
	
}




add_shortcode("WGcpt", "main_function"); //註冊shortcode
?>