  $('.tab-content').hide();
  $('.tab-content:nth-child(1)').show();

  //點擊後尋找到對應內容
  $('#tabs-nav li').click(function(){
  $('.tab-content').hide();
  var activeTab = $(this).find('a').attr('href');
  $(activeTab).show();
});