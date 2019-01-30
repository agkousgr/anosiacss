import swal from 'sweetalert2';

const WOW = require('wowjs');
require('owl.carousel');
// let apex = require('./jquery.apex-slider.js');
// let apexslider = apex.apexSlider();
let bestSellersContainer = $('#best-sellers-container');
require('webpack-jquery-ui/autocomplete');
// require('jquery-autocomplete');


window.wow = new WOW.WOW({
    live: false,
    animateClass: 'animated',
    offset: 100,
    callback: function (box) {
        console.log("WOW: animating <" + box.tagName.toLowerCase() + ">")
    }
});

window.wow.init();

// wow = new WOW(
//     {
//         animateClass: 'animated',
//         offset: 100,
//         callback: function (box) {
//             console.log("WOW: animating <" + box.tagName.toLowerCase() + ">")
//         }
//     }
// );
// wow.init();

$(document).ready(function () {
    let owl = $('.LatestProductsScroll');
    owl.owlCarousel({
        margin: 30,
        nav: true,
        loop: true,
        responsive: {
            0: {items: 1},
            480: {items: 1},
            768: {items: 1},
            992: {items: 3},
            1200: {items: 4}
        }
    })
})

$(document).ready(function () {
    let owl = $('.OfferCarousel');
    owl.owlCarousel({
        margin: 30,
        nav: true,
        loop: true,
        responsive: {
            0: {items: 1},
            480: {items: 1},
            768: {items: 1},
            992: {items: 3},
            1200: {items: 3}
        }
    })
})

$(document).ready(function () {

    $('.best-sellers-tab').on('click', function () {
        let data = {
            'ctgId': $(this).data('id')
        };
        loadBestSeller(data);
    });
    let data = {
        'ctgId': 1578
    };
    loadBestSeller(data);

});

$(document).ready(function () {

    $("#anosia-keyword").autocomplete({
        // source: ['test', 'zong'],
        source: Routing.generate('search_for_anosia'),
        select: function (event, ui) {
            event.preventDefault();
            $("#anosia-keyword").val(ui.item.label);
            let url = Routing.generate('products_list', {'slug': ui.item.value});
            window.location.assign(url);
        },
        focus: function (event, ui) {
            event.preventDefault();
            $("#anosia-keyword").val(ui.item.label);
        },
        minLength: 3
    });

    $('.best-sellers-category').on('click', function (e) {
        e.preventDefault();
        let curId = $(this).data('id');
        $('.resp-tabs-list').each(function () {
            if (curId == $(this).data('id')) {
                $(this).removeClass('hidden');
            } else {
                $(this).addClass('hidden');
            }
        })
        let data = {
            'ctgId': $(this).data('firstchild')
        };
        loadBestSeller(data);
    });

    $('#best-seller-products').on('click', '.add-to-cart', function (e) {
        e.preventDefault();
        let data = {
            'id': $(this).data('id'),
            'quantity': 1,
            'name': $(this).data('name')
        }
        $.post(Routing.generate('add_to_cart'), data, function (result) {
            if (result.success && result.exist === false) {
                // swal({
                //     title: 'Καλάθι',
                //     html: '<div style="font-size:17px;">Το προϊόν ' + result.prName + ' προστέθηκε με επιτυχία!</div>',
                //     type: 'success',
                //     timer: 5000
                // });
                $('#cart-total-items').css('display', 'inline');
                $('#cart-total-items').html(result.totalCartItems);
                $("#sidebar-cart-wrapper").toggleClass("active");
                $('#collapseCart').load(Routing.generate('load_top_cart'));
                setTimeout(function () {
                    $("#sidebar-cart-wrapper").toggleClass("active")
                }, 3000);
            } else if (result.success && result.exist === true) {
                swal({
                    title: 'Ουπς',
                    html: '<div style="font-size:17px;">Το προϊόν ' + result.prName + ' υπάρχει ήδη στο καλάθι σας!</div>',
                    type: 'info',
                    timer: 5000
                });
            } else {
                swal({
                    title: 'Ουπς',
                    html: '<div style="font-size:17px;">Κάποια σφάλμα παρουσιάστηκε!</div>',
                    type: 'error',
                    timer: 5000
                });
            }
        });
    });

    // FACEBOOK
    // FB.getLoginStatus(function (response) {
    //     statusChangeCallback(response);
    // });
    //
    // function checkLoginState() {
    //     FB.getLoginStatus(function (response) {
    //         statusChangeCallback(response);
    //     });
    // }
    //
    // FB.login(function (response) {
    //     if (response.authResponse) {
    //         console.log('Welcome!  Fetching your information.... ');
    //         FB.api('/me', function (response) {
    //             console.log('Good to see you, ' + response.name + '.');
    //         });
    //     } else {
    //         console.log('User cancelled login or did not fully authorize.');
    //     }
    // }, {scope: 'public_profile,email'});
    //
    // // This is called with the results from from FB.getLoginStatus().
    // function statusChangeCallback(response) {
    //     console.log('statusChangeCallback');
    //     console.log(response);
    //     // The response object is returned with a status field that lets the
    //     // app know the current login status of the person.
    //     // Full docs on the response object can be found in the documentation
    //     // for FB.getLoginStatus().
    //     if (response.status === 'connected') {
    //         // Logged into your app and Facebook.
    //         // testAPI();
    //     } else {
    //         // The person is not logged into your app or we are unable to tell.
    //         document.getElementById('fb-root').innerHTML = 'Please log ' +
    //             'into this app.';
    //     }
    // }

    // $('body').startComponents();
});

function loadBestSeller(data) {
    $.post(Routing.generate('home_best_seller'), data, function (html) {
        $('#best-seller-products').empty().html(html);
        $('.product-main-slider').show();
        let owl = $('.ProductScrollTab');
        owl.owlCarousel({
            margin: 30,
            nav: true,
            loop: true,
            responsive: {
                0: {items: 1},
                480: {items: 1},
                768: {items: 1},
                992: {items: 3},
                1200: {items: 3}
            }
        })
    });
}
// function generate(type, Message) {
//     var n = noty({
//         text: Message,
//         theme: 'relax',
//         type: type,
//         animation: {
//             open: 'animated bounceInLeft', // Animate.css class names
//             close: 'animated bounceOutRight', // Animate.css class names
//             easing: 'swing', // unavailable - no need
//             speed: 500 // unavailable - no need
//         },
//         timeout: 2100,
//     });
//     console.log('html: ' + n.options.id);
// }