jQuery(document).ready(function($) {

    $('.summary').append( $('.product-badge') );
    $("option:contains('Choose an option')").remove();
    $(".guarantee").insertAfter('.single_add_to_cart_button'); 
    
    var priceRange = jQuery('.summary .price').text();
    var lastChar = priceRange.substr(priceRange.length -1);
    if ( lastChar === '$') {
        var newPrice = priceRange.slice(0,-3);
        jQuery('.summary .price').html(newPrice);
    }

    jQuery('li.product').each(function() {
        var priceRangeList = jQuery('.product-list-price',this).text();
        var lastChar = priceRangeList.substr(priceRangeList.length -1);
        if ( lastChar === '$') {
            var newPrice2 = priceRangeList.slice(0,-3);
            jQuery('.product-list-price',this).html(newPrice2);
        }
    })


    jQuery('.product-detail').each(function() {
        var priceSidebar = jQuery('.product-list-price ',this).text();
        var lastChar = priceSidebar.substr(priceSidebar.length -1);
        if ( lastChar === '$') {
            var newPrice3 = priceSidebar.slice(0,-3);
            jQuery('.product-list-price',this).html(newPrice3);
        }
    })

	$("#product_cat option[value='uncategorized']").remove();
	$('#page-1515 .page-headline').attr('style','padding-top:40px!important');

    var wh = $(window).height();
    $('.place-map').height(wh-120);

    $(".dropdown_product_cat option[value='featured-on-home-page']").remove();

    // .ready allows to do some stuff just after the parse has finished building DOM tree
    // from the html page
    // images may have not yet been loaded therefore their dimensions may not be known yet
    // Note that $ alias is available because it is passed to this function as a parameter
    // by jQuery event handler

    //$("<p>Don't forget to add some stuff to jquery-functions.js</p>").insertBefore("body > div.wrapper");


    $("#club_shipping_date").next().remove();
    $("#club_shipping_date").remove();
    $("#billing_company_field").remove();
    $("#shipping_company_field").remove();
    $("#shipping_country_field").css('display','none');
    //var address = $("#billing_address_1_field") + $("#billing_address_2_field");
    //$("#billing_address_2_field").insertAfter('#billing_last_name_field');
    //$("#billing_address_1_field").insertAfter('#billing_last_name_field');
    $('#billing_phone_field').insertAfter('#billing_postcode_field');
    $('#billing_email_field').insertAfter('#billing_postcode_field');
    $('.shop_page .page-title').insertBefore('.shop_page .product-sidebar');
    $('.woocommerce-message').insertBefore('.product-sidebar');
	$('#selected_shipping_field').insertAfter('#billing_email_field');
	$('.woocommerce-shipping-fields').insertAfter('.woocommerce-additional-fields');
	$('.woocommerce-Reviews-title').text('Share your thoughts!');
	
	$('.woocommerce-form-coupon-toggle').insertAfter('#customer_details');
	$('.woocommerce-form-coupon').insertAfter('.woocommerce-form-coupon-toggle');
    
	//alert("say something");
    var imageWidth = $('.single-gallery-image ').width();
    var screenWidth = $(window).width();
	
	$('.woocommerce-shipping-totals td').text('Proceed to checkout to view shipping options');

    $('.single-gallery-image ').height(imageWidth);
    $(window).resize(function(){
        var imageWidth = $('.single-gallery-image ').width();
        var screenWidth = $(window).width();
        $('.single-gallery-image ').height(imageWidth);
    });


    if ( screenWidth < 1024 ) {
        $('.single-product-page .product-sidebar').insertAfter('.checkout-link');
        $('.product-sidebar').css('float','none');
    }
    $(window).resize(function(){
        var screenWidth = $(window).width();
        if ( screenWidth < 1024 ) {
            $('.single-product-page .product-sidebar').insertAfter('.checkout-link');
            $('.product-sidebar').css('float','none');
        }
    });


    $(window).on('load', function() {
        $(".value").on('change','select', function(){
            $('.summary.entry-summary p.price').fadeOut();
        });
        $(".inactive_message_container").insertAfter("#page-116 .checkout-link");
    });
	
	
	// Email signup popup 
	$('.click-to-signup').click(function() {
		$('.subs-popup').css('display','block');
	});
	
	$('.email-close').click(function() {
		$('.subs-popup').css('display','none');
	})
		
		/*
		$('#signup-form').submit( function(event) {
			var email = $('#mailchimp-email').val();
			if ( email === '' || !isValidEmailAddress(email) ) {
				$('#mailchimp-email').css('background-color','#ec7575');
				event.preventDefault();
			}
		}
								 
		function isValidEmailAddress(emailAddress) {
    		var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    		return pattern.test(emailAddress);
		}
		*/

    /*
    // Sticky header
     if($(window).scrollTop() === 0) {
            $('.header').animate({
            height: '126px'
        }, 200);
     }

    $(window).scroll(function() {
        $('.header').animate({
            height: '90px'
        }, 200);
        $('.logo img').animate({
            height: '90px'
        }, 200);
    })
    */


    /*
$(function(){
  $('#header').data('size','big');
});

$(window).scroll(function(){
  if($(document).scrollTop() > 0) {
    if($('#header').data('size') == 'big') {
        $('#header').data('size','small');
        $('#header').stop().animate( {
            'height':'90px'
        }, 600);
        $('.logo img').stop().animate( {
            height:'90px'
        }, 600);
        $('.menu-main').stop().animate( {
            'padding-top':'11px'
        }, 600);
        $('.page-hero').stop().animate( {
            'margin-top':'0px'
        }, 600);
        $('.header .inner .header-right .menu-main ul li ul').stop().animate( {
            'margin-top':'22px'
        }, 600);
        $('.cart-icon-container').stop().animate( {
            'padding-top':'8px'
        }, 600);
        $('#responsive-menu-pro-button').stop().animate( {
            'top':'12px'
        }, 600);
        $('#header').css('background-color','rgba(0,49,106,.9)');
    }
}

else
  {
    if($('#header').data('size') == 'small') {
        $('#header').data('size','big');
        $('#header').stop().animate( {
            height:'131px'
        }, 600);
        $('.logo img').stop().animate( {
            height:'120px',top:'0'
        }, 600);
        $('.menu-main').stop().animate( {
            'padding-top':'24px'
        }, 600);
        $('.sub-menu').stop().animate( {
            'margin-top':'50px'
        }, 600);
        $('.page-hero').stop().animate( {
            'margin-top':'131px'
        }, 600);
        $('.place-map').stop().animate( {
            'margin-top':'131px'
        }, 600);
        $('.cart-icon-container').stop().animate( {
            'padding-top':'28px'
        }, 600);
        $('#responsive-menu-pro-button').stop().animate( {
            'top':'32px'
        }, 600);
        $('#header').css('background-color','rgba(0,49,106,1)');
      }
  }
});
*/
/*$('#responsive-menu-pro-button').click(function() {
    $(this).css('transform','translate(0px)');
})*/

$("#woocommerce_product_categories-4 option[value='featured-on-home-page']").remove();
$("li.cat-item-62").remove();

$('.hamburger-menu').click(function() {
  $( '.mobile-menu' ).animate({left: '0'});
});

$('.close-btn').click(function() {
  $( '.mobile-menu' ).animate({left: '100%'});
});

$(".mobile-menu .menu-item-has-children").append("<div class='open-menu-link open'> + </div>");
$('.mobile-menu .menu-item-has-children').append("<div class='open-menu-link close'> - </div>");
$('.open').addClass('visible');
$('.open-menu-link').click(function(e) {
    var childMenu = e.currentTarget.parentNode.children[1];
    if ($(childMenu).hasClass('visible')) {
        $(childMenu).removeClass("visible");
        $(e.currentTarget.parentNode.children[3]).removeClass("visible");
        $(e.currentTarget.parentNode.children[2]).addClass("visible");
    } else {
        $(childMenu).addClass("visible");
        $(e.currentTarget.parentNode.children[2]).removeClass("visible");
        $(e.currentTarget.parentNode.children[3]).addClass("visible");
    }
});

// Email Sign-up Overlay
// Stopping this as we are now offering the newsletter signup via clicking on a message in the header
/*
idleTime = 0;
$limit = 3;
if (Cookies.get('test_status') != '1') {
	console.log('hello');
	$.get('/sign-up-form', function(data) {
		$('.subs-popup').html(data);
		
		$('.button').click(function() {
			$('.subs-popup').hide();
			$limit = 9999;
		});
	});
	function timerIncrement() {
		idleTime = idleTime + 1;
		if (idleTime > $limit) { 
			$('.subs-popup ').show();
			idleTime = 0;
		}
	}

	var idleInterval = setInterval(timerIncrement, 6000); // 60 seconds

	Cookies.set('test_status', '1', { expires: 1 });
} 
*/
// Email Sign-up End
	
// Refer a friend overlay
//.refer-button 
//.refer-overlay
//.refer-overlay-inner

$('.refer-button').click(function() {
	$('.refer-overlay').css('display','block');
	$('.refer-overlay-inner').css('display','block');
})
$('.refer-overlay').click(function() {
	$('.refer-overlay').css('display','none');
	$('.refer-overlay-inner').css('display','none');
})	

$("#free-shipping-diff").clone().insertAfter("#order_review_heading");
$(".checkout #free-shipping-diff").css('color','red');

$("#shipping_method_0").next().css('display','none');

$(".star-rating span").addClass("fas"); 
	
// category page product review excerpts
//var containerHeight = $('.category-comments-container').height(); $('.single-comment').height(containerHeight - 30);

// Put category description inside the product category loop ul. Not ideal
var catDescription = $('.woocommerce-category-description'); 
$('.woocommerce-category-description').remove(); 
$('.products.columns-4').append(catDescription);

var catOut = $('.out-of-stock-container'); 
$('.out-of-stock-container').remove(); 
$('.products.columns-4').append(catOut);

	
});




(function($) {
    // This block will execute when jquery-functions.js file will referenced from the page header
    // Here you can define functions that can use $ alias for jQuery.

    console.log('jQuery version: ' + $.fn.jquery);
})(jQuery);
