jQuery(document).ready(function($) {
    
    
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
    

    //alert("say something");
    var imageWidth = $('.single-gallery-image ').width();
    var screenWidth = $(window).width();
    
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

$('#responsive-menu-pro-button').appendTo('.header-right');
$('#responsive-menu-pro-button').click(function() {
    $(this).css('transform','translate(0px)');
})
    
$("#woocommerce_product_categories-4 option[value='featured-on-home-page']").remove();
$("li.cat-item-62").remove();    
    
 

    
    
    
});




(function($) {
    // This block will execute when jquery-functions.js file will referenced from the page header
    // Here you can define functions that can use $ alias for jQuery.
    
    console.log('jQuery version: ' + $.fn.jquery);
})(jQuery);