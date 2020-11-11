jQuery(document).ready(function ($) {

    var smb = $('#filter_date'),
        smbtax = $('#filter_date_in_tax');
        check = $('#pdf_gen_id');

    $(check).on('click',function () {
        if($(this).is(':checked')){
            smb.val('Get PDF');
            smbtax.val('Get PDF');
            $("#filter_form").attr("target", "_blank");
        }else{
            smb.val('Filter');
            smbtax.val('Filter');
            $("#filter_form").removeAttr("target");
        }
    });

    $('#filter_date_manager_after').datetimepicker({
        controlType: 'select',
        oneLine: true,
        timeFormat: 'hh:mm tt',
        dateFormat: 'mm-dd-yy',
    });
    $('#filter_date_manager_before').datetimepicker({
        controlType: 'select',
        oneLine: true,
        timeFormat: 'hh:mm tt',
        dateFormat: 'mm-dd-yy',
    });

    $('#club-selected-options').multiSelect({
        selectableHeader: "<div class='ms-custom-header'>Choose a User</div>",
        selectionHeader: "<div class='ms-custom-header'>Users who will be added</div>"
    });
    $('#lummi_email_sender').multiSelect({
        selectableHeader: "<div class='ms-custom-header'>Choose addresses</div>",
        selectionHeader: "<div class='ms-custom-header'>Will send to these addresses.</div>"
    });

    $('#select-all').click(function(){
        $('#lummi_email_sender').multiSelect('select_all');
        return false;
    });
    $('#deselect-all').click(function(){
        $('#lummi_email_sender').multiSelect('deselect_all');
        return false;
    });

    var url = window.location.href;

    var tax_club = "taxonomy=clubs",
        tax_all = "taxonomy=";

    if(url.indexOf(tax_club) != -1){
        var startDateTextBox = $('#clubs_open_date');
        var endDateTextBox = $('#clubs_close_date');

        $.timepicker.datetimeRange(
            startDateTextBox,
            endDateTextBox,
            {
                controlType: 'select',
                oneLine: true,
                minInterval: (1000*60*60),
                timeFormat: 'hh:mm tt',
                dateFormat: 'mm-dd-yy',
                start: {},
                end: {}
            }
        );
    }


    $('#clubs_ship_date').datepicker({
        dateFormat: 'mm-dd-yy'
    });

    $('#clubs_arrival_date').datepicker({
        dateFormat: 'mm-dd-yy'
    });

    if(url.indexOf(tax_club) !== -1){
        $('#col-container').before('<button class="show_hide_button" title="Show the new clubs controls">Show the new clubs controls<span></span></button>');
        var event_button = $('.show_hide_button'),
            col_left = $('#col-left');

        event_button.on('click',function () {
           if(col_left.css("visibility") == "visible"){
               $(this).text('Show the new clubs controls');
               $(this).prop('title','Show the new clubs controls');
               col_left.addClass('inactive_col');
               col_left.removeClass('active_col');
           }else{
               $(this).text('Hide the new clubs controls');
               $(this).prop('title','Hide the new clubs controls');
               col_left.addClass('active_col');
               col_left.removeClass('inactive_col');
           }
        });
    }

    $('#edittag').before('<span class="scroll_to">Scroll to orders</span>');

    function gotoTarget(go,to){
        $(go).on('click',function() {
            $('html, body').animate({
                scrollTop: $(to).offset().top
            }, 1800);
        });
    }
    gotoTarget('.scroll_to','.lw-taxonomy-orders-container');

    $('.email_invitation_header #clubs_selector').on('change',function(){
        var club_id = $(this).find(":selected").val(),
            club_name = $(this).find(":selected").text();
        if(club_id){
            $('[name="choice_club"]').val(club_id);
            var und_text = club_name.replace(/ /g,"_");
            $('[name="choice_club_slug"]').val(und_text);
        }
    });

    // jQuery('#chefs_club_table input[name="chefs_prc"]').keyup(function () {
    //     this.value = this.value.replace(/[^0-9\.]/g,'');
    // });

    jQuery('[id^="chefs_club_id_"]').keyup(function () {
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });

    $('[id^="chefs_club_id_"]').on('keyup',function(){
        $('#chefs_club_option .save_chefs_product').prop('disabled',false);
    });

    $('#chefs_club_table').on('change',function(){
        $('#apply_chefs').prop('disabled',false);
    });
    
    /**
     * Set chefs percent in clubs taxonomy
     */
    $('#apply_chefs').on('click',function (ev) {
        ev.preventDefault();

        var active = $('#chefs_club_table input[type="radio"]'),
            chefs_prc = $('#chefs_club_table input[name="chefs_prc"]').val(),
            chefs_prc_oldval = $('#chefs_club_table input[name="chefs_prc"]').data('old-value'),
            club_id = $('#chefs_club_table input[name="club_id"]').val(),
            chefs_active = '';

        $.each(active,function(i,v){
            if($(v).is(":checked")){
                chefs_active = parseInt($(v).val());
            }
        });

        var current_active = $('[name="current_active"]');

        var txt = '';

        if( isNaN(parseFloat( chefs_prc ) ) && parseInt( chefs_active ) === 1 ){
            alert('Field percent can\'t be empty');
            return false;
        }

        if( parseInt( current_active.val() ) === 1 && parseInt( chefs_active ) === 0 ){

            txt = 'All chef percentage will be removed from products!\nAre you sure?';

            var quest = confirm(txt);

            if( quest === false){
                return quest;
            }
        }

        if( parseInt( current_active.val() ) === 1 && parseInt(current_active.val()) === parseInt(chefs_active) ){

            if(parseFloat(chefs_prc) === parseFloat(chefs_prc_oldval)){
                alert('No changes');
                return false;
            }

            if(chefs_prc !== chefs_prc_oldval){
                txt = 'This action will override chef club percent in all products with this value: '+ chefs_prc +'%.\nAre you sure?';
            }

            var quest = confirm(txt);

            if( quest === false){
                return quest;
            }
        }

        $('.chef-loader-image .chef_done').remove();
        $('.chef-loader-image').addClass('loaded');

        $.ajax({
            url: chefs.admin_url,
            method: 'POST',
            data:{
                action: chefs.action_club,
                chefs_nonce_club: chefs.chefs_nonce_club,
                chefs_active: chefs_active,
                chefs_prc: chefs_prc,
                club_id: club_id
            },
            dataType : 'json'
        }).done(function (response) {
            $('.chef-loader-image').removeClass('loaded');
            $('.chef-loader-image').append(response.message);
            $('#apply_chefs').prop('disabled',true);
        });
    });
    
    /**
     * Save chefs percent in product
     */
    $('.save_chefs_product').on('click',function (ev) {
        ev.preventDefault();

        $('#chefs_club_option .save_chefs_product').prop('disabled',true);
        $('#chefs_club_option .load-image').removeClass('check');
        $('#chefs_club_option .load-image').addClass('load');


        var  chefs_prc = $('input[name^="chefs_club_id_"]'),
            product_id = $('#chefs_club_option input[id^="chefs-post-id-"]').val();

        $.ajax({
            url: chefs.admin_url,
            method: 'POST',
            data:{
                action: chefs.action_product,
                chefs_nonce_product: chefs.chefs_nonce_product,
                chefs_prc: chefs_prc.serialize(),
                product_id: product_id
            },
            dataType : 'json'
        }).done(function (response) {
            if(response.success === true){
                $('#chefs_club_option .load-image').removeClass('load');
                $('#chefs_club_option .load-image').addClass('check');
            }
        });
    });
    
    /**
     * Save period if set to club, but no periods in DB
     */
    $('input[name="force-period"]').on('click',function(){
        
        var open_date = $('#clubs_open_date').val(),
            close_date = $('#clubs_close_date').val(),
            club_id = $('input[name="club_id"]').val(),
            club_name = $('#name').val(),
            club_slug = $('#slug').val();
            
            $.ajax({
                url: chefs.admin_url,
                method: 'POST',
                data:{
                    action: chefs.action_set_period,
                    club_set_period_nonce: chefs.club_set_period_nonce,
                    club_name: club_name,
                    club_slug : club_slug,
                    open_date : open_date,
                    close_date : close_date,
                    club_id: club_id
                },
                dataType : 'json'
            }).done(function (response) {
                
                if ( response.success === true ){
                    location.reload();
                }else{
                    $('.unsaved-period').html('<span style="color: red;">'+ response.data.message +'</span>');
                }
                
            });
    });
    
    /**
     * Delete club history
     */
    $('#btn-delete-history').on('click',function(){
        
        var del_this = $('#clubs-documents-section').find('input[name="del-period-row"]:checked');
        var item_num = $('.displaying-num .item-num');

        if(del_this.length === 0){
            return false;
        }
        
        var del = confirm('Are you sure!');

        if( del === false ){
            return del;
        }
        
        var obj = {};
        var tr_del = [];
        
        del_this.each(function(i,v){
            var tr = $('#clubs-documents-section tr[data-row="'+$(v).val()+'"]');
            tr.delay(200 * i).css({backgroundColor: 'rgba(255, 0, 0, 0.2)'}).fadeOut(100);
            tr_del[i] = tr;
            obj[i] = $(v).val();
        });
        
        $.ajax({
                url: chefs.admin_url,
                method: 'POST',
                data:{
                    action: chefs.action_delete_period,
                    club_delete_period_nonce: chefs.club_delete_period_nonce,
                    row_id: obj
                },
                dataType : 'json'
            }).done(function (response) {
                
                $('.del-message').html('Deleted rows: ' + response.num_rows);
                
                setTimeout(function(){
                    for (j = 0; j < tr_del.length; j++ ){
                        tr_del[j].remove();
                    }
                }, 1000);
                item_num.html( parseInt(item_num.text() - parseInt(response.num_rows) ) );
        });
       
    });

    $(".show-walkthrough").on('click',function (ev) {
        var button = $(this),
            wt = $(".video-walkthrough");
            if(wt.is(":hidden")){
                wt.slideDown();
            }else{
                wt.slideUp();
            }
    });

    if ( window.location.hash ) scroll(0,0);
    // void some browsers issue
    // setTimeout( function() { scroll(0,0); }, 1);

    $(function() {
        // *only* if we have anchor on the url
        if(window.location.hash) {

            // smooth scroll to the anchor id
            $('html, body').animate({
                scrollTop: $(window.location.hash).offset().top + 'px'
            }, 0, 'swing');
        }
    });

    /**
     * Set cookie
     * @param {type} name
     * @param {type} value
     * @param {type} days
     * @returns {undefined}
     */
    function createCookie(name,value,days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        }
        else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
    }
    /**
     * Delete cookie
     * @param {type} name
     * @returns {undefined}
     */
    function eraseCookie(name) {
        createCookie(name, "", -1);
    }
    
    /**
     * Filter clubs in history and set cookie with selected club id
     */
    $('.choice-club').on('change',function(e){
        var club_id = parseInt($(this).find(":selected").val()),
            cookie_name = 'HCLUB_ID';

        if(isNaN(club_id)){
            eraseCookie(cookie_name);
            window.location.replace(chefs.history_url);
            return;
        }

        createCookie(cookie_name,club_id);
        window.location.replace(chefs.history_url + '&club_id=' + club_id +'');
    });

});