jQuery(document).ready(function ($) {
    
    if(lwu.message && !lwu.id_page || (lwu.id_shop === lwu.id_page )){
        $('.page-title').remove();
        $('.woocommerce-info').remove();

        var idcontent = $('#content'),
            classcontent = $('.content'),
            wrapper = $('.wrapper');

        if(idcontent.length > 0){
            idcontent.prepend('<div class="inactive_message_container"><p class="the_inactive_message">'+ lwu.message +'</p></div>');
        }else if(classcontent.length > 0){
            classcontent.prepend('<div class="inactive_message_container"><p class="the_inactive_message">'+ lwu.message +'</p></div>');
        }
        else if(wrapper.length > 0){
            wrapper.prepend('<div class="inactive_message_container"><p class="the_inactive_message">'+ lwu.message +'</p></div>');
        }
    }
});