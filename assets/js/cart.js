$(document).ready(function () {

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

})