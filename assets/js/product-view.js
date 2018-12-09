require('./jquery.bxslider.min.js');
require('jquery-zoom');

$(document).ready(function () {
    $('.bxslider').bxSlider({
        pagerCustom: '#bx-pager',
        auto: false,
    });

    let url;
    $('.product-photo')
        .wrap('<span style="display:inline-block"></span>')
        .css('display', 'block')
        .parent()
        .zoom({
            url: $(this).find('img').attr('data-zoom')
        });
})