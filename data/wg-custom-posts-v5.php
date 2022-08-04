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
//一共5個function  分別用途：1.主程式整合 2.(後端)使用者輸入name轉成slug 3.(後端)轉換後的slug進行搜尋商家 4.(前端)顯示商家資訊 5.引入外部CSS檔
/*整合用主程式*/
function main_function($userInput)
{
	$cptSlug = 'company'; //想搜尋cpt的slug
	$cptTaxSlug = 'loan'; //想搜尋cpt內taxonomy的slug
	$post_per_page = -1; //文章呈現數量

	//引數處理
	$taxName = shortcode_atts(array(
		'tax' => '', 
		'name' => '', 
	),  $userInput);

	//判斷使用者輸入為何，進入不同模式
	if ($taxName['name'] !== '')
	{
		$mode = 1; //存在使用者輸入name 比對taxslug底下是否有部分相符的字串
	}
	elseif ($taxName['tax'] !== '')
	{
		$mode = 2; //存在使用者輸入tax 轉換使用者輸入
	}
	else
	{
		$mode = 0;
	}

	//依據使用者輸入 分配至不同流程
	switch ($mode)
	{
		case 0:
			return "請輸入tax或name進行搜尋";
			break;
		//模糊比對
		case 1:
			$taxSlug = findTaxSlug($taxName['name'], $cptSlug, $cptTaxSlug, $mode);//比對CPT底下是否有部分字串符合的Taxonomy 透過轉換將name轉成slug
			$content = cptQuery($cptSlug, $taxSlug, $cptTaxSlug, $post_per_page, $mode);//進行商家搜尋，不同城市存入不同的陣列中
			$html = getNameContent($content);//前端顯示
			return $html;
			
		//完全比對
		case 2:
			$taxSlug = findTaxSlug($taxName['tax'], $cptSlug, $cptTaxSlug, $mode); //轉換使用者輸入 
			$content = cptQuery($cptSlug, $taxSlug, $cptTaxSlug, $post_per_page, $mode); //搜尋文章分類下的店家資訊	
			$html = getTaxContent($content); //前端顯示
			return $html;
				
		default:
			return "main程式有誤，需進行除錯";
	}
	
}




/*mode12：搜尋文章分類下的店家資訊*/
function cptQuery($cptSlug, $taxSlug, $cptTaxSlug, $post_per_page, $mode)
{
	switch ($mode)
	{
		case 1:
			$content = [[[]]];
			$city = '';
			foreach ($taxSlug as $value)
			{
				$splitArray = explode('-',$value);
				$city = $splitArray[0];

				$query_string = array(
					'post_type' => $cptSlug,  //cpt slug的名稱
					$cptTaxSlug => $value,  //tax slug名稱
					'post_per_page' => $post_per_page, 
				);
				$queryCategory = new WP_Query($query_string); //搜尋分類內容
				$i=0;
				//存在post時執行
				if ($queryCategory -> have_posts())
				{
					
					while ($queryCategory -> have_posts())
					{
						$queryCategory -> the_post(); //指向下一篇
						$content[$city][$i]['featuredImg'] = get_the_post_thumbnail_url(get_the_ID(), 'full');  //取得特色圖片
						$content[$city][$i]['title'] = get_the_title(); //取得標題
						$content[$city][$i]['link'] = get_the_permalink(); //取得網址
						$i++;
					}
					
				}
				else
				{
					break;
				}

			}
			return $content;
			
		case 2:
			$content = [[]]; //放內容標題、圖片網址、連結
	
			$query_string = array(
				'post_type' => $cptSlug,  //cpt slug的名稱
				$cptTaxSlug => $taxSlug,  //tax slug名稱
				'post_per_page' => $post_per_page, 
			);
			
			$queryCategory = new WP_Query($query_string); //搜尋分類內容
			
			$i=0;
			//存在post時執行
			if ($queryCategory -> have_posts())
			{
				while ($queryCategory -> have_posts())
				{
					$queryCategory -> the_post(); //指向下一篇
					$content[$i]['featuredImg'] = get_the_post_thumbnail_url(get_the_ID(), 'full');  //取得特色圖片
					$content[$i]['title'] = get_the_title(); //取得標題
					$content[$i]['link'] = get_the_permalink(); //取得網址
					$i++;
				}
			}
			return $content;

		default:
			return "cpt程式有誤，需進行除錯";
			break;
	}
}



/*mode12：轉換使用者輸入 name -> slug*/
function findTaxSlug($userInput, $cptSlug, $cptTaxSlug, $mode) 
{
	$term = [[]];
	$findCompareSlug = [];
	//搜尋條件
	$query_string = array(
		'post_type' => $cptSlug, 
		'post_per_page' => -1, 
		 $cptTaxSlug => '', 
   	);
	
	$queryCategory = new WP_Query($query_string);
  	if ($queryCategory -> have_posts())
	{
		$i=0;
		//取得所有post的分類(slug與中文分類名)
		while ($queryCategory -> have_posts())
		{		
			$queryCategory -> the_post(); //指向下篇
			$cur_id = get_the_ID(); //取得id
			$terms = get_the_terms($cur_id, $cptTaxSlug); //搜尋附加至term的相關資訊
			//找到所有post的slug與name存起來
			foreach ($terms as $value)
			{
				$term[$i]['name'] = $value -> name;
				$term[$i]["slug"] = $value -> slug;
				$i++;
			}
		}
		
		switch ($mode)
		{
			case 1:
				
				//比對輸入字串與taxonomy部分符合
				foreach ($term as $value)
				{
					$i = 0;
					if (stripos($value['name'], $userInput) !== false)
					{
						$findCompareSlug[] = $value['slug']; //找到部分相符的name 將對應的slug存入陣列中 
						$i++;
					}		
				}
				if ($findCompareSlug !== null)
				{
					return $findCompareSlug;//傳回slug
				}

			case 2:
				//比對輸入字串與taxonomy完全相符
				foreach ($term as $value)
				{
					if ($value['name'] == $userInput) 
					{
						return $value['slug']; //找到相符的name 把對應的slug傳入function
					}		
				}

			default:
				return "findTaxSlug程式有誤，需進行除錯";
				break;

		}

	
	}
	else
	{
		return false;
	}

}

/*mode1：前端顯示部分*/
function getNameContent($content)
{
	//var_dump($content);
	$city = ["khh", "thh", "cyi", "chw", "txg", "hsz"]; //按鈕對應的city id
	//上方導覽列
	$show = "<div class='tabs-container'>";
	$show .= "<ul id='tabs-nav'><li><a href='#khh'>高雄市</a></li><li><a href='#thh'>台南市</a></li><li><a href='#cyi'>嘉義市</a></li><li><a href='#chw'>彰化市</a></li><li><a href='#txg'>台中市</a></li><li><a href='#hsz'>新竹市</a></li></ul>";
	
	$show .= "<div id='tabs-content'>"; //外層包全部的店家
	
	//6個縣市執行6次
	foreach ($city as $cityValue)
	{
		$i = 0;
		if ($content[$cityValue] !== null)
		{
			$show .= "<div id='".$cityValue."' class='tab-content'>";
			$show .= "<div class='grid-container'>";

			foreach ($content[$cityValue] as $value)
			{
				$show .= "<div class='grid-item'>";//包單一商家的圖片與文字

				$show .= "<div class='grid-item-img'>";
				$show .= "<a href='".$content[$cityValue][$i]['link']."'>";
				$show .= "<img src ='".$content[$cityValue][$i]['featuredImg']."'>"; //圖片
				$show .= "</a>";
				$show .= "</div>";

				$show .= "<div class='grid-item-text'>";
				$show .= "<a href='".$content[$cityValue][$i]['link']."'>";
				$show .= "<p>".$content[$cityValue][$i]['title']."</p>"; //文字
				$show .= "</a></div>";
			
				$show .= "</div>";
				$i++;
			}
			$show .= "</div></div>";
			
		}
		

	}
		

	$show .= "</div></div>";
	return $show;
	
}

/*mode2：前端顯示部分*/
function getTaxContent($content)
{
	
	//echo "<link rel='stylesheet' type='text/css' media='screen' href='style_aspect.css'>"; //標頭檔外部引入自訂的css檔
	$show = "<div class='grid-container'> "; //外層包全部的店家
	foreach ($content as $value)
	{
		$show .= "<div class='grid-item'>";//包單一個商家的圖片與文字
		$show .= "<div class='grid-item-img'>";
		$show .= "<a href='".$value['link']."'>";
		$show .= "<img src ='".$value['featuredImg']."'>"; //圖片
		$show .= "</a>";
		$show .= "</div>";
		$show .= "<div class='grid-item-text'>";
		$show .= "<a href='".$value['link']."'>";
		$show .= "<p>".$value['title']."</p>"; //文字
		$show .= "</a>";
		$show .= "</div>";
		$show .= "</div>";
	}

	$show .= "</div>";
	return $show;
}

add_shortcode("loan-co", "main_function"); //註冊shortcode

/*加入css檔*/
add_action('wp_head', 'wg_custom_posts_css');
function wg_custom_posts_css()
{
	echo "<link rel=stylesheet type='text/css' href='https://loan97.net/wp-content/plugins/wg-custom-posts/assets/css/wg-cpt-grid.css'>";
}
/*加入js檔*/
add_action('wp_footer', 'wg_custom_posts_js');
function wg_custom_posts_js()
{
	echo "<script type='text/javascript' src='https://loan97.net/wp-content/plugins/wg-custom-posts/assets/js/tabs.js'>";
}
?>