import swal from 'sweetalert2';

$(document).ready(function () {
    $('#top-cart').hide();

    $('#update-cart-btn').on('click', function (e) {
        e.preventDefault();
        $('form[name="cart-items"]').submit();
    });

    $('#use-coupon').on('click', function (e) {
        e.preventDefault();
        let data = {
            'coupon': $('#discount-coupon').val()
        }
        // console.log(coupon);
        $.post(Routing.generate('check_coupon'), data, function (result) {
            if (result.success === true) {
                location.reload();
            } else {
                swal({
                    title: 'Ουπς',
                    html: '<div style="font-size:17px;">Το κουπόνι που εισάγατε δεν είναι έγκυρο!</div>',
                    type: 'error',
                    timer: 5000
                });
            }
        })
    });

    $('#remove-coupon').on('click', function (e) {
        e.preventDefault();
        let data = {
            'coupon': $('#coupon-name').text()
        }
        // console.log(coupon);
        $.post(Routing.generate('remove_coupon'), data, function () {
            location.reload();
        })
    })

    $('.add-to-cart-inner').on('click', function (e) {
        e.preventDefault();
        let quantity = 1;
        let data = {
            'id': $(this).data('id'),
            'quantity': quantity,
            'name': $(this).data('name')
        }
        $.post(Routing.generate('add_to_cart'), data, function (result) {
            if (result.success && result.exist === false) {
                location.reload();
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
})