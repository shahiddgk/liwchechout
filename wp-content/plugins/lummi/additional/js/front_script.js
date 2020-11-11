jQuery(document).ready(function($) {

    if(log.message && log.id_shop === "1" && log.status === "0"){

        var idcontent = $('#content'),
            classcontent = $('.content'),
            wrapper = $('.wrapper');

        if(idcontent.length > 0){
            idcontent.prepend('<div class="inactive_message_container"><p class="the_inactive_message">'+ log.message +'</p></div>');
        }else if(classcontent.length > 0){
            classcontent.prepend('<div class="inactive_message_container"><p class="the_inactive_message">'+ log.message +'</p></div>');
        }
        else if(wrapper.length > 0){
            wrapper.prepend('<div class="inactive_message_container"><p class="the_inactive_message">'+ log.message +'</p></div>');
        }
    }

});