/*
  控制 tab 顯示程式
*/

//20220422修改按鈕變色與下方對應商家一致

var areaList = ["north", "center", "south", "east"];
var defaultCity = ["tpe", "txg", "khh", "ttt"];

/*隱藏所有內容 呈現有商家資訊的內容*/
jQuery(".area.tab-content").hide(); //隱藏所有內容
jQuery(".area.tab-content:nth-child(1)").show(); //顯示第一個地區內容


/*取得呈現縣市內容的id 利用字串處理將對應a href 加入active屬性 由css控制按鈕顏色*/
var areaId = jQuery(".area.tab-content:nth-child(1)").attr("id");
var area = "#area-tabs-nav li a[href$= '" + areaId + "-wg']";
jQuery(area).addClass("active");
jQuery(".city.tab-content").hide(); //隱藏所有內容

// 選取每個地區的預設縣市
areaList.forEach((area, key) => {
    // 若預設縣市無資料則抓取第一個有資料縣市為預設
    var cityId = jQuery(`.city.tab-content.${area}:nth-child(1)`).attr("id");
    // 取得該地區所有Tab
    var cityList = jQuery(`.city.tab-content.${area}`);
    for( var i = 0; i < cityList.length ; i++ ){
        if( cityList[i].id === defaultCity[key] ){
            cityId = cityList[i].id;
            break;
        }
    }
    // 將預設縣市設為active
    var city = "#city-tabs-nav li a[href$= '" + cityId + "-wg']";
    jQuery(city).addClass("active");
    jQuery(` #${cityId}.city.tab-content`).show(); //顯示預設縣市商家內容

});

/*縣市連結被點擊處理事件*/
jQuery("#city-tabs-nav li a").click(function () {
    var activeTab = jQuery(this).attr("href").split("-")[0]; //利用字串處理取得商家的class
    var area = jQuery(this).attr("id").split("-")[0];
    jQuery(`#city-tabs-nav.${area} li a`).removeClass("active"); //移除所有有active class的selector
    jQuery(this).addClass("active"); //將被點擊者加上active屬性
    jQuery(`.city.tab-content.${area}`).hide(); //隱藏所有下方商家資訊
    jQuery(activeTab).show(); //呈現class對應的商家內容
});

// 地區連結被點擊處理
jQuery("#area-tabs-nav li a").click(function () {
    jQuery("#area-tabs-nav li a").removeClass("active"); //移除所有有active class的selector
    jQuery(this).addClass("active"); //將被點擊者加上active屬性
    jQuery(".area.tab-content").hide(); //隱藏所有下方縣市資訊
    var activeTab = jQuery(this).attr("href").split("-")[0]; //利用字串處理取得縣市的class
    jQuery(activeTab).show(); //呈現class對應的縣市內容
});
