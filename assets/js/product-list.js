require('ion-rangeslider');

$(document).ready(function () {
    let queryParameters = {}, queryString = location.search.substring(1),
        re = /([^&=]+)=([^&]*)/g, m;

// Creates a map with the query string parameters
    while (m = re.exec(queryString)) {
        queryParameters[decodeURIComponent(m[1])] = decodeURIComponent(m[2]);
    }


    let urlPage = '';
    let url = 'http://' + window.location.hostname + window.location.pathname;
    let data = {};
    data['page'] = getUrlParameter('page');
    data['pagesize'] = getUrlParameter('pagesize');
    data['brands'] = getUrlParameter('brands');
    data['sortBy'] = getUrlParameter('sortBy');

    $.each(data, function (key, value) {
        urlPage = key + '=' + value + '&';
    });
    if (urlPage.length > 0) {
        urlPage = urlPage.slice(0, -1);
        url = url + '?' + urlPage;
    }
    console.log(url);

    $("#range_slider").ionRangeSlider({
        type: "double",
        min: 1,
        max: 250,
        // min: $('input[name="minPrice"]').val(),
        // max: $('input[name="maxPrice"]').val(),
        from: $('input[name="lowPrice"]').val(),
        to: $('input[name="highPrice"]').val(),
        prefix: "",
        postfix: "",
        decorate_both: true
    });

    $('#price-range').on('click', function () {
        queryParameters['priceRange'] = $('.irs-from').text() + '-' + $('.irs-to').text();
        location.search = $.param(queryParameters); // Causes page to reload
    });

    $('select[name="sorting"]').on('change', function () {
        queryParameters['sortBy'] = $(this).val();
        location.search = $.param(queryParameters); // Causes page to reload
    });

    $('#brand-filter').on('click', function () {
        let brandIds = '';
        $('.brand-chk').each(function () {
            if ($(this).prop('checked') == true) {
            brandIds = brandIds + $(this).attr('id') + '-';
            }
        });
        queryParameters['brands'] = brandIds.slice(0,-1);
        location.search = $.param(queryParameters); // Causes page to reload
    });


    $('select[name="maxItemPerPage"]').change(function () {
        queryParameters['pagesize'] = $(this).val();
        location.search = $.param(queryParameters); // Causes page to reload
    });

});

let getUrlParameter = function getUrlParameter(sParam) {
    let sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

