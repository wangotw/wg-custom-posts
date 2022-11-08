<?php

/*轉換使用者輸入 name -> slug*/
function findTaxSlug($userInput, $cptSlug, $cptTaxSlug, $mode) 
{
	$term = [[]]; //模糊比對中文名與存在slug的對應陣列
	$findCompareSlug = []; //★回傳值，儲存所有符合比對的slug，用於搜尋商家
	//搜尋條件
	$query_string = array(
		'post_type' => $cptSlug, 
		'posts_per_page' => -1, 
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
		wp_reset_postdata();
		switch ($mode)
		{
			case "part":
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
				if (!empty($findCompareSlug))
				{
					return $findCompareSlug;//傳回slug
				}
			break;
			case "full":
				//比對輸入字串與taxonomy完全相符
				foreach ($term as $value)
				{
					if ($value['name'] == $userInput) 
					{
						return $value['slug']; //找到相符的name 把對應的slug傳入function
					}		
				}
			break;
			default:
				return "findTaxSlug程式有誤，需進行除錯";
				break;

		}

	
	}

}

?>