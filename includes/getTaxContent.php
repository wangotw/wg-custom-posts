<?php

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


?>