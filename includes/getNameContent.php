<?php


/*mode1：模糊比對、前端顯示*/
function getNameContent($content)
{
	$city = ["khh", "tnn", "cyi", "chw", "txg", "hsz"]; //按鈕對應的city id
	//上方導覽列
	$show = "<div class='tabs-container'>";
	$show .= "<ul id='tabs-nav'><li><a href='#khh". "-wg" ."'>高雄市</a></li><li><a href='#tnn". "-wg" ."'>台南市</a></li><li><a href='#cyi". "-wg" ."'>嘉義市</a></li><li><a href='#chw". "-wg" ."'>彰化市</a></li><li><a href='#txg". "-wg" ."'>台中市</a></li><li><a href='#hsz". "-wg" ."'>新竹市</a></li></ul>";
	$show .= "<div id='tabs-content'> "; //外層包全部的店家
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


?>