jQuery(document).ready(function(){
	showBuyPopUP();
	function showBuyPopUP(){
		//if(jQuery('.showBuy').length > 0){
//			jQuery('.showBuy').mouseenter(function(){
//				jQuery('.popupContBuy').removeClass('hidden');
//				jQuery('.popupContBuy').css({
//					'top': jQuery('#price_tag').position().top - 9
//				});
//			});
//			jQuery('.popupContBuy').mouseenter(function(){
//				jQuery('.popupContBuy').removeClass('hidden');
//			});
//			jQuery('.showBuy').mouseleave(function(){
//				jQuery('.popupContBuy').addClass('hidden');
//			});
//			jQuery('.popupContBuy').mouseleave(function(){
//				jQuery('.popupContBuy').addClass('hidden');
//			});
			var maskLayer = jQuery('#maskLayer');
			if(maskLayer.length == 0) {
				var maskLayer = jQuery('<div id="maskLayer"></div>').appendTo(document.body);
			}
			maskLayer.css({
				'position': 'fixed',
				'zIndex': 9,
				'height': '100%',
				'width': jQuery(window).width(),
				'top':0,
				'left':0,
				'background-image':'url("components/com_enmasse/images/modal_bg.png")',
				'display':'none'
			});

			jQuery('#price_tag_cont  .showBuy').click(function(){
				jQuery('.popupContBuy').addClass('hidden');
				jQuery('#popupContBuynormal').removeClass('hidden');
				maskLayer.css('display', 'block');
				jQuery('#popupContBuynormal').css({
					'top': jQuery('#price_tag').position().top - 9
				});
				return false;
			});
			
			
			jQuery('#for_a_friend .showBuy').click(function(){
				jQuery('.popupContBuy').addClass('hidden');
				jQuery('#popupContBuyFriend').removeClass('hidden');
				maskLayer.css('display', 'block');
				jQuery('#popupContBuyFriend').css({
					'top': jQuery('#price_tag').position().top - 9
				});
				return false;
			});
			jQuery('.popupContBuy #enmasse_attr_close').click(function(){
				jQuery('.popupContBuy').addClass('hidden');
				jQuery('#maskLayer').css('display', 'none');
				return false;
			});
			jQuery('#maskLayer').click(function(){
				jQuery('.popupContBuy').addClass('hidden');
				jQuery('#maskLayer').css('display', 'none');
				return false;
			});
		//}
	}
	jQuery(document.body).bind('click', function(e){
		
	});
});

