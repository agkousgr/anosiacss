require('./jquery.bxslider.js');
require('jquery-zoom');

$(document).ready(function () {
    $('.bxslider').bxSlider({
        pagerCustom: '#bx-pager',
        auto: false,
        // onSliderLoad: function() {
        //     $('.product-main-slider').find('.bx-loading').removeClass('bx-loading');
        //     coc
        // }
    });

    // console.log($('.product-photo').find('img'));
    if ($('.product-photo').find('img')) {
        let url;
        $('.product-photo')
            .wrap('<span style="display:inline-block"></span>')
            .css('display', 'block')
            .parent()
            .zoom({
                url: $(this).find('img').attr('data-zoom')
            });
    }
    // $('.product-main-slider').find('.bx-loading').removeClass('bx-loading');
})