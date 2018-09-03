require('ion-rangeslider');

$(document).ready(function () {
    $("#range_slider").ionRangeSlider({
        type: "double",
        min: 10,
        max: 250,
        from: 100,
        to: 175,
        prefix: "Τιμή: ",
        postfix: " €",
        decorate_both: true
    });

    $('select[name="sorting"]').on('change', function () {
        window.location.replace($('option:selected', this).attr('href'));
    });

    $('select[name="#maxItemPerPage"]').change(function(){

        let url = '{{path("controller_index_route","maxItemPerPage":_itemNum)}}';
        let item = $('select[name="#maxItemPerPage"]').find(":selected").val();

        window.location.href = url.replace('_itemNum',item );
    })

});