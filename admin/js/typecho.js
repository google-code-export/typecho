/*
 * You can add any Javascript here.
 */

$(document).ready(function() {
	$("table.latest tr").mouseover(function() {  
		 $(this).addClass("over"); }).mouseout(function() { 
			 $(this).removeClass("over"); });
	$("table.latest tr:even").addClass("alt");
	$("table.setting tr:odd").addClass("alt");

	$(":text").addClass("text");
	$(":password").addClass("password");
	$(":submit").addClass("submit");
	$(":button").addClass("button");
});

