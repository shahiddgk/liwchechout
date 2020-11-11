jQuery(document).ready(function($) {
    if(regi.club_cook){

        function createRegCookie(name,value,days) {
            if (days) {
                var date = new Date();
                date.setTime(date.getTime()+(days*24*60*60*1000));
                var expires = "; expires="+date.toGMTString();
            }
            else var expires = "";
            document.cookie = name+"="+value+expires+"; path=/";
        }

        createRegCookie('club_reg',regi.club_cook);

    }
});