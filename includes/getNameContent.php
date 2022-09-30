<?php
// 定義各縣市地區對照表
define("area_list", array("north", "center", "south", "east"));

define("tw_city_name", array(
    "north" => ["臺北市", "新北市", "基隆市", "宜蘭縣", "桃園市", "新竹市"],
    "center" => ["苗栗縣", "臺中市", "彰化縣", "南投縣", "雲林縣"],
    "south" => ["嘉義市", "台南市", "高雄市", "屏東市"],
    "east" => ["花蓮縣", "台東縣"]
));

define("tw_city_code", array(
    "north" => ["tpe", "ntpc", "kel", "ila", "tyn", "hsz"],
    "center" => ["zmi", "txg", "chw", "ntc", "yun"],
    "south" => ["cyi", "tnn", "khh", "pif"],
    "east" => ["hun", "ttt"]
));

/*
    格式 :
    $content = [
        "tpe" => [
            "0" => [
                "link" => string,
                "featuredImg" => string,
                "title" => string
            ],
        ],
    ]

*/

/*mode1：模糊比對、前端顯示*/
function getNameContent($content)
{
    // 先建立地區Tab
    $show = "<div class='tabs-container'>";
    $show .= "<ul id='area-tabs-nav'><li><a href='#north" . "-wg" . "'>北部</a></li><li><a href='#center" . "-wg" . "'>中部</a></li><li><a href='#south" . "-wg" . "'>南部</a></li><li><a href='#east" . "-wg" . "'>東部</a></li></ul>";
    $show .= "<div id='area-tabs-content'> "; // 外層包所有縣市
    // 針對每個地區建立各縣市Tab
    foreach (area_list as $area) {
        $show .= "<div id='" . $area . "' class='tab-content area'>";
        $show .= "<div class='tabs-container'>";
        $show .= "<ul id='city-tabs-nav' class='" . $area . "'>";
        // 有資料的縣市才會顯示Tab
        foreach (tw_city_code[$area] as $key => $city) {
            if (array_key_exists($city, $content)) {
                $show .= "<li><a href='#" . $city . "-wg' Id='" . $area . "-herf" . "'>" . tw_city_name[$area][$key] . "</a></li>";
            }
        }
        $show .= "</ul>";
        $show .= "<div id='city-tabs-content'>"; //內層包全部的店家
        foreach (tw_city_code[$area] as $key => $city) {
            // 有資料的縣市才會顯示
            if (array_key_exists($city, $content)) {
                $show .= "<div id='" . $city . "' class='tab-content city " . $area . "'>";
                $show .= "<div class='grid-container'>";

                foreach ($content[$city] as $key => $value) {
                    $show .= "<div class='grid-item'>"; //包單一商家的圖片與文字

                    $show .= "<div class='grid-item-img'>";
                    $show .= "<a href='" . $content[$city][$key]['link'] . "'>";
                    $show .= "<img src ='" . $content[$city][$key]['featuredImg'] . "'>"; //圖片
                    $show .= "</a>";
                    $show .= "</div>";

                    $show .= "<div class='grid-item-text'>";
                    $show .= "<a href='" . $content[$city][$key]['link'] . "'>";
                    $show .= "<p>" . $content[$city][$key]['title'] . "</p>"; //文字
                    $show .= "</a></div>";

                    $show .= "</div>";
                }
                $show .= "</div></div>";
            }
        }
        $show .= "</div></div></div>";
    }

    $show .= "</div></div>";
    return $show;
}
