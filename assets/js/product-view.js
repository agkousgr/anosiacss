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

    $('#add-quantity').on('blur', function () {
        if ($(this).val() > $(this).attr('max')) {
            $(this).val($(this).attr('max'));
        }
    })
    // $('.product-main-slider').find('.bx-loading').removeClass('bx-loading');
})