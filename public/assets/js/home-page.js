$(document).ready(function () {
    let cartContainer = $('#collapseCart');

    cartContainer.on('click', '.remove-item', function (e) {
        e.preventDefault();
        let data = {
            'id': $(this).data('id'),
            'name': $(this).data('name')
        }
        $.post(Routing.generate('delete_top_cart_item'), data, function (result) {
            if (result.success) {
                swal({
                    title: 'Καλάθι',
                    html: '<div style="font-size:17px;">Το προϊόν ' + result.prName + ' διαγράφηκε με επιτυχία!</div>',
                    type: 'success',
                    timer: 5000
                });
                if (result.totalCartItems == 0) {
                    $('#cart-total-items').css('display', 'none');
                }
                $('#cart-total-items').html(result.totalCartItems);
                $('#collapseCart').load(Routing.generate('load_top_cart'));
            } else {
                swal({
                    title: 'Ουπς',
                    html: '<div style="font-size:17px;">Κάποια σφάλμα παρουσιάστηκε!</div>',
                    type: 'error',
                    timer: 5000
                });
            }
        });
    })

    $('#collapseCart').load(Routing.generate('load_top_cart'));
    if ($('input[name="cart"]').val() == 1) {
        $('#top-cart').css('display', 'none');
    }

    // FACEBOOK
    FB.getLoginStatus(function(response) {
        statusChangeCallback(response);
    });

    function checkLoginState() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    }

    FB.login(function(response){
        if (response.authResponse) {
            console.log('Welcome!  Fetching your information.... ');
            FB.api('/me', function(response) {
                console.log('Good to see you, ' + response.name + '.');
            });
        } else {
            console.log('User cancelled login or did not fully authorize.');
        }
    }, {scope: 'public_profile,email'});

    // This is called with the results from from FB.getLoginStatus().
    function statusChangeCallback(response) {
        console.log('statusChangeCallback');
        console.log(response);
        // The response object is returned with a status field that lets the
        // app know the current login status of the person.
        // Full docs on the response object can be found in the documentation
        // for FB.getLoginStatus().
        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            testAPI();
        } else {
            // The person is not logged into your app or we are unable to tell.
            document.getElementById('fb-root').innerHTML = 'Please log ' +
                'into this app.';
        }
    }

    // $('body').startComponents();
});

$('.add-to-cart').on('click', function (e) {
    e.preventDefault();
    let quantity = 1;
    if ($('#add-quantity').val()) {
        quantity = $('#add-quantity').val();
    } else {
        quantity = 1;
    }
    let data = {
        'id': $(this).data('id'),
        'quantity': quantity,
        'name': $(this).data('name')
    }
    $.post(Routing.generate('add_to_cart'), data, function (result) {
        if (result.success && result.exist === false) {
            swal({
                title: 'Καλάθι',
                html: '<div style="font-size:17px;">Το προϊόν ' + result.prName + ' προστέθηκε με επιτυχία!</div>',
                type: 'success',
                timer: 5000
            });
            $('#cart-total-items').css('display', 'inline');
            $('#cart-total-items').html(result.totalCartItems);
            $('#collapseCart').load(Routing.generate('load_top_cart'));
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


// product view add to cart
$('.add-to-cart-view').on('click', function (e) {
    e.preventDefault();
    // alert($(this).data('id') + ' | ' + $('#add-quantity').val());
    $('#collapseCart').load(Routing.generate('add_to_cart', {
        'id': $(this).data('id'),
        'quantity': $('#add-quantity').val()
    }));
});

$('.owl-dots').css('display', 'none');

// WISH LIST
$('.add-to-wishlist').on('click', function () {
    e.preventDefault();
    // alert($(this).data('id'));
    $('#collapseCart').load(Routing.generate('add_to_wishlist', {'id': $(this).data('id')}));
});


$(".toggle-sidebar").click(function (e) {
    e.preventDefault();
    $("#sidebar-wrapper").toggleClass("active");
});


// LOGIN
$('.btn-signin').on('click', function (e) {
    e.preventDefault();
    let data = {
        'username': $('input[name="_username"]').val(),
        'password': $('input[name="_password"]').val(),
    }
    $.post(Routing.generate('login'), data, function (result) {
        if (result.success) {
            Routing.generate('index');
        }else{
            swal({
                title: 'Είσοδος χρήστη',
                html: '<div style="font-size:17px;">' + result.errorMsg + '</div>',
                type: 'error',
                timer: 10000
            });
        }
    });
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