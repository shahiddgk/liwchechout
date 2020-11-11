(function($){

    $.fn.email_invitation_changer = function(options){

        var value,
            lwopt = $.extend({
            target: '',
            event: 'change',
        }, options );

        $(this).on(lwopt.event,function() {
            value = $(this).val();
            $.post(
                ei.base_url,
                {
                    action : 'ajaxGetUsersClubInfo',
                    club_id : value
                },function(response) {

                $(lwopt.target).empty();
                $('#ms-lummi_email_sender .ms-list').empty();

                var item = null;

                for (item in response){
                    $(lwopt.target).multiSelect('addOption',{value: response[item].user_email,text: response[item].user_email +" - "+ response[item].user_name });
                }
            });
        });

    };

    $('#clubs_selector').email_invitation_changer({
        target: '#lummi_email_sender'
    });

}(jQuery));

