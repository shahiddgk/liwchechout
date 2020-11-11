jQuery(document).ready(function($) {

	$(".toplevel_page_manage-club-home #ID span").text(function () {
		return $(this).text().replace("Remove User", "Select a user"); 
	});
	
	$(".toplevel_page_manage-club-home .column-ID span").text(function () {
		return $(this).text().replace("Remove User", ""); 
	});
	
	$(".toplevel_page_manage-club-home #ID .sorting-indicator").css('display','none');
	$(".toplevel_page_manage-club-home .column-ID .sorting-indicator").css('display','none');
	
});