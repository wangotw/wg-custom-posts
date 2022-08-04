//20220422修改按鈕變色與下方對應商家一致

/*隱藏所有內容 呈現有商家資訊的內容*/
jQuery('.tab-content').hide(); //隱藏所有內容
jQuery('.tab-content:nth-child(1)').show(); //顯示第一個商家內容

/*取得呈現商家內容的id 利用字串處理將對應a href 加入active屬性 由css控制按鈕顏色*/
var cityId = jQuery('.tab-content:nth-child(1)').attr('id');
var city = "#tabs-nav li a[href$= '" + cityId + "-wg']";
jQuery(city).addClass('active');

/*上方連結被點擊處理事件*/
jQuery('#tabs-nav li a').click(function(){
  jQuery('#tabs-nav li a').removeClass('active'); //移除所有有active class的selector
  jQuery(this).addClass('active'); //將被點擊者加上active屬性
  jQuery('.tab-content').hide(); //隱藏所有下方商家資訊
  var activeTab = jQuery(this).attr('href').split('-')[0]; //利用字串處理取得商家的class
  jQuery(activeTab).show(); //呈現class對應的商家內容
	
});