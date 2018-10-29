require('./jquery.bxslider.min.js');

$(document).ready(function () {
    $('.bxslider').bxSlider({
        pagerCustom: '#bx-pager',
        auto: false,
    });
})