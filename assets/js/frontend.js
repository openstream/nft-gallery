jQuery(function ($) {
  	$(document).ready(function() {
		$('.nft').on('click', function() {
		    window.open($(this).attr('data-url'), '_blank');
		});
  });
});