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