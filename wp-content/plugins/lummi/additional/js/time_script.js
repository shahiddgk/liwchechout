jQuery(document).ready(function($){

    var dst;
    if ( dt.la_time ) {
        dst = "PDT";
    } else {
        dst = "PST";
    }

    var date = new Date(dt.origin_date + ' ' + dst);

    var datetime = moment(date).format('L') +' '+ moment(date).format('LT');

    $('#date_result').html(datetime);
});
