<?php

/*搜尋文章分類下的店家資訊*/
function cptQuery( $cptSlug, $taxSlug, $cptTaxSlug, $post_per_page, $mode ) {

	switch ( $mode ) {
		case "part":
			$content = [[[]]]; //儲存前端呈現資料
			$city = ''; //儲存有資料的城市
			$exist_company = []; //儲存已存在公司，用以比對
			foreach ($taxSlug as $value)
			{
				$splitArray = explode('-',$value); //以"-"分割，如：txg-land-second-mortgage  [0]->txg [1]->land ...
				$city = $splitArray[0]; //僅取得城市分類的slug名稱
				$i = 0;
				$query_string = array(
					'post_type' => $cptSlug,  //cpt slug的名稱
					$cptTaxSlug => $value,  //tax slug名稱
					'posts_per_page' => $post_per_page, //搜尋所有文章
				);
				$queryCategory = new WP_Query($query_string); //搜尋分類內容

				//存在post時執行
				if ($queryCategory -> have_posts()) {
					while ($queryCategory -> have_posts()) {
						$queryCategory -> the_post(); //指向下一篇
						$title = get_the_title();
						if ( in_array( $city . $title, $exist_company ) == False ){
							$content[$city][$i]['featuredImg'] = get_the_post_thumbnail_url(get_the_ID(), 'full');  //取得特色圖片
							$content[$city][$i]['title'] = $title; //取得標題
							$content[$city][$i]['link'] = get_the_permalink(); //取得網址
							$i++;
							array_push( $exist_company, $city . $title );
						}
					}
				}
				wp_reset_postdata();
			}
			return $content;
			break;
		case "full":
			$content = [[]]; //放內容標題、圖片網址、連結
			/*Taxonomy Slug若為空值，不執行Query Loop*/
			if ( !isset($taxSlug) ) {
				return;
			}
			$query_string = array(
				'post_type' => $cptSlug,  //cpt slug的名稱
				$cptTaxSlug => $taxSlug,  //tax slug名稱
				'posts_per_page' => $post_per_page, 
			);

			$queryCategory = new WP_Query($query_string); //搜尋分類內容
			$j=0;
			if ($queryCategory -> have_posts()) {
				while ($queryCategory -> have_posts())
				{
					$queryCategory -> the_post(); //指向下一篇
					$content[$j]['featuredImg'] = get_the_post_thumbnail_url(get_the_ID(), 'full');  //取得特色圖片
					$content[$j]['title'] = get_the_title(); //取得標題
					$content[$j]['link'] = get_the_permalink(); //取得網址
					$j++;
				}
			}
			wp_reset_postdata();
			return $content;
			break;
	}
}

?>