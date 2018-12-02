// let apex = require('./jquery.apex-slider.js');
// let apexslider = apex.apexSlider();
let bestSellersContainer = $('#best-sellers-container');
require('webpack-jquery-ui/autocomplete');
// require('jquery-autocomplete');
$(document).ready(function () {
    // console.log('ok');
    let data = {
        'ctgId': 1728
    };
    $('#best-seller-products').load(Routing.generate('home_best_seller', data, function(html) {
        console.log(html);
    }));
});

$(document).ready(function () {

    $("#anosia-keyword").autocomplete({
        // source: ['test', 'zong'],
        source: Routing.generate('search_for_anosia'),
        select: function (event, ui) {
            event.preventDefault();
            $("#anosia-keyword").val(ui.item.label);
            // let url = Routing.generate('products_list', {'id': ui.item.value});
            // window.location.assign(url);
        },
        focus: function(event, ui) {
            event.preventDefault();
            $("#anosia-keyword").val(ui.item.label);
        },
        minLength: 3
    });

    // bestSellersContainer.on('click', '.best-sellers-subctg', function (e) {
    //     e.preventDefault();
    //     console.log($(this).data('id'));
    //     let data = {
    //         'ctgId': $(this).data('id')
    //     };
    //     $('#best-seller-products').load(Routing.generate('home_best_seller', data, function() {
    //         $('.owl-carousel').addClass('owl-loaded owl-drag');
    //     }));
    // });

    // $("#anosia-keyword").on('change', function(e) {
    //     e.preventDefault();
    //     window.location(Routing.generate('product_list', {'id':}));
    // });

    // let api;
    // $(document).ready(function () {
    //     api = $(".fullwidthbanner").apexslider({
    //         startWidth: 1170,
    //         startHeight: 893,
    //         transition: "random",
    //         thumbWidth: 100,
    //         thumbHeight: 47,`
    //         thumbAmount: 0,
    //         navType: "both",
    //         navStyle: "round",
    //         navArrow: "visible",
    //         showNavOnHover: true,
    //         timerAlign: "bottom",
    //         shadow: 0,
    //         fullWidth: true
    //     });
    // });

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