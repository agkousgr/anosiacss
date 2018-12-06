require('./jquery.bxslider.min.js');
require('jquery-zoom');

$(document).ready(function () {
    $('.bxslider').bxSlider({
        pagerCustom: '#bx-pager',
        auto: false,
    });

    $(document).ready(function() {
        var owl = $('#bx-pager');
        owl.owlCarousel({
            margin:10,
            nav: true,
            loop: false,
            autoplay: false,
            responsive: {
                0: {items: 3},
                640: {items: 4},
                768: {items: 3},
                992: {items: 4},
            }
        })
    })

    $('.product-photo')
        .wrap('<span style="display:inline-block"></span>')
        .css('display', 'block')
        .parent()
        .zoom({
            url: $(this).find('img').attr('data-zoom')
        });
})