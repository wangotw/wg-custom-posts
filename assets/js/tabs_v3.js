jQuery('.tab-content').hide();
jQuery('.tab-content:nth-child(1)').show();

jQuery('#tabs-nav a').click(function(){
  jQuery('.tab-content').hide();
  var activeTab = jQuery(this).attr('href').split('-')[0];
  jQuery(activeTab).show();
});