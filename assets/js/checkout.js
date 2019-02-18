// $('.step-edit').on('click', function (e) {
//     let data = {}
//     data['currentStep'] = $(this).attr('id'),
//     $.post(Routing.generate('checkout'), data, function (result) {
//         if (result.success) {
//             swal({
//                 title: 'Είσοδος χρήστη',
//                 html: '<div style="font-size:17px;">Συνδεθήκατε με επιτυχία!</div>',
//                 type: 'success',
//                 timer: 10000
//             });
//             location.reload();
//         } else {
//             swal({
//                 title: 'Είσοδος χρήστη',
//                 html: '<div style="font-size:17px;">' + result.errorMsg + '</div>',
//                 type: 'error',
//                 timer: 10000
//             });
//         }
//     })
// })
require('bootstrap');
require('jquery-validation');

$(document).ready(function () {

    if ($('#total-cost').data('cost') <= 0) {
        $('#checkout-footer').text('Για να ολοκληρώσετε την παραγγελία σας, θα πρέπει να προσθέσετε στο καλάθι σας προϊόντα αξίας ίσης η μεγαλύτερης των ' + Math.abs($('#total-cost').data('cost')) + '€');
        $('#checkout-footer').addClass('flash-notice');
    }

    $('#checkout_step2_paymentType_0').prop('checked', true);
    $('#checkout_step1_shippingType_0').prop('checked', true);
    // $('form[name="checkout_step1"]').validate();

    // validate signup form on keyup and submit
    $('form[name="checkout_step1"]').validate({
        rules: {
            'checkout_step1[firstname]': "required",
            'checkout_step1[lastname]': "required",
            // username: {
            //     required: true,
            //     minlength: 2
            // },
            // password: {
            //     required: true,
            //     minlength: 5
            // },
            // confirm_password: {
            //     required: true,
            //     minlength: 5,
            //     equalTo: "#password"
            // },
            'checkout_step1[email]': {
                required: true,
                email: true
            },
            'checkout_step1[address]': 'required',
            'checkout_step1[zip]': 'required',
            'checkout_step1[city]': 'required',
            'checkout_step1[district]': 'required',
            'checkout_step1[phone01]': 'required',
            'checkout_step1[afm]': 'required',
            'checkout_step1[irs]': 'required',
            // checkout_step1_newsletter: {
            //     required: "#newsletter:checked",
            //     minlength: 2
            // },
            // checkout_step1_newsletter: "required"
        },
        messages: {
            'checkout_step1[firstname]': "Παρακαλώ συμπληρώστε το όνομά σας",
            'checkout_step1[lastname]': "Παρακαλώ συμπληρώστε το επώνυμό σας",
            // username: {
            //     required: "Please enter a username",
            //     minlength: "Your username must consist of at least 2 characters"
            // },
            // password: {
            //     required: "Please provide a password",
            //     minlength: "Your password must be at least 5 characters long"
            // },
            // confirm_password: {
            //     required: "Please provide a password",
            //     minlength: "Your password must be at least 5 characters long",
            //     equalTo: "Please enter the same password as above"
            // },
            'checkout_step1[email]': "Παρακαλώ εισάγεται μια έγκυρη διεύθυνση email",
            'checkout_step1[address]': 'Παρακαλώ συμπληρώστε τη διεύθυνσή σας',
            'checkout_step1[zip]': 'Παρακαλώ συμπληρώστε τον ταχυδρομικό σας κώδικα',
            'checkout_step1[city]': 'Παρακαλώ συμπληρώστε την πόλη σας',
            'checkout_step1[district]': 'Παρακαλώ συμπληρώστε την περιοχή σας',
            'checkout_step1[phone01]': 'Παρακαλώ συμπληρώστε το τηλέφωνό σας',
            'checkout_step1[afm]': 'Παρακαλώ συμπληρώστε το ΑΦΜ σας',
            'checkout_step1[irs]': 'Παρακαλώ συμπληρώστε τη ΔΟΥ σας',
            // agree: "Please accept our policy",
            // topic: "Please select at least 2 topics"
        },
        submitHandler: function () {
            let form = $('#checkout-personal-information-step').find('form');
            let formData = form.serialize();
            $.post(Routing.generate('step1_submit'), formData, function (response) {
                if (response.success == false) {
                    swal({
                        title: 'Σφάλμα',
                        html: '<div style="font-size:17px;">' + response.errorMsg + '</div>',
                        type: 'error',
                        timer: 5000
                    });
                } else {
                    $('#checkout-personal-information-step').addClass('hidden');
                    $('#checkout-payment-step').removeClass('hidden');
                    $('#checkoutStep2').removeClass('disabled');
                    $("html, body").animate({scrollTop: 0}, 1000);
                }
            });
        }
    });

    $('#checkout_step1_email').focusout(function () {
        let data = {
            'email': $(this).val()
        };
        $.post(Routing.generate('check_if_user_exists'), data, function (response) {
            if (response.success == false) {
                swal({
                    title: 'Σφάλμα email',
                    html: '<div style="font-size:17px;">' + response.errorMsg + '</div>',
                    type: 'error',
                    timer: 5000
                });
                $('#confirm-addresses').attr('disabled', 'disabled');
            } else {
                $('#confirm-addresses').removeAttr('disabled');
            }
        });

    });

    $('form[name="checkout_step2"]').submit(function (e) {
        e.preventDefault();
    }).validate({
        rules: {
            'checkout_step2[agreeTerms]': "required",
        },
        messages: {
            'checkout_step2[agreeTerms]': "Για να ολοκληρωθεί η παραγγελία σας, αποδεχτείτε τους όρους χρήσης του site",
        },
        submitHandler: function () {
            // $('form[name="checkout_step2"]').submit();
            if ($('#checkout_step2_paymentType_2').prop('checked') === true) {
                console.log('pireaus');
                $('#pireaus_container').show();
                $('#pireaus-post').submit();
            } else {
                console.log('not pireaus');
                let form = $('form[name="checkout_step2"]');
                let formData = form.serialize();
                $.post(Routing.generate('checkout'), formData, function (result) {
                    if (result.success === true) {
                        let url = Routing.generate('complete_checkout');
                        window.location.assign(url);
                    }
                });
            }

        }
    });
    // $('#confirm-addresses').on('click', function (e) {
    //     // e.preventDefault();
    //     $('#checkout-personal-information-step').addClass('hidden');
    //     $('#checkout-payment-step').removeClass('hidden');
    //     $('#checkoutStep2').removeClass('disabled');
    //     $("html, body").animate({scrollTop: 0}, 1000);
    //     // return false;
    //     //
    //     // // // $('#tab-2').trigger('click');
    //     // //
    //     // let form = $('#checkout-personal-information-step').find('form');
    //     // let formData = form.serialize();
    //     // $.post(Routing.generate('checkout'), formData, function(response) {
    //     //
    //     // });
    // });

    $('#checkoutStep1').on('click', function (e) {
        e.preventDefault();
        $('#checkout-personal-information-step').removeClass('hidden');
        $('#checkout-payment-step').addClass('hidden');
        $('#checkoutStep2').addClass('disabled');
        // $("html, body").animate({scrollTop: 0}, 1000);
        // return false;
    })


    $('[data-toggle="popover"]').popover();

    $('input[name="address"]').on('click', function () {
        let data = {
            'id': $(this).data('id')
        };
        $.post(Routing.generate('get_address'), data, function (response) {
            if (response.success == true) {
                $('#checkout_step1_shipAddress').val(response.address["address"]);
                $('#checkout_step1_shipZip').val(response.address["zip"]);
                $('#checkout_step1_shipCity').val(response.address["city"]);
                $('#checkout_step1_shipDistrict').val(response.address["district"]);
            } else {
                swal({
                    title: 'Σφάλμα!',
                    text: "Παρουσιάστηκε σφάλμα κατά την επιλογή υπάρχουσας διεύθυνσης!",
                    type: 'cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonText: 'Κλείσιμο',
                })
            }
        });
    });

    $('input[name="checkout_step1[series]"]').on('change', function () {
        $('#invoice-fields').toggleClass('hidden');
        if ($('#checkout_step1_series_0').is(':checked')) {
            $('#checkout_step1_afm').val('No invoice');
            $('#checkout_step1_irs').val('No invoice');
        } else {
            $('#checkout_step1_afm').val('');
            $('#checkout_step1_irs').val('');
        }
    });

    $('input[name="checkout_step1[shippingType]"]').on('change', function () {
        let totalCost = 0;
        let couponDisc = 0;
        if ($('#coupon-discount').length > 0) {
            couponDisc = $('#coupon-discount').data('discount');
        }
        if ($('#checkout_step1_shippingType_0').is(':checked')) {
            if ($('#cart-cost').data('cost') - couponDisc <= 39) {
                $('#shipping-cost').data('cost', 2.00);
                $('#shipping-cost').html('2,00');
                totalCost = $('#cart-cost').data('cost') + 2 - couponDisc;
            } else {
                $('#shipping-cost').data('cost', 0.00);
                $('#shipping-cost').html('0,00');
                totalCost = $('#cart-cost').data('cost') - couponDisc;
            }
            $('.checkout_step1_shippingType_1').addClass('hidden');
            $('.checkout_step2_paymentType_1').addClass('hidden');
            $('[for="checkout_step2_paymentType_4"]').removeClass('hidden');
            $('[for="checkout_step2_paymentType_1"]').addClass('hidden');
        } else {
            $('#shipping-cost').data('cost', 0.00);
            $('#shipping-cost').html('0,00');
            totalCost = $('#cart-cost').data('cost') - couponDisc;
            $('.checkout_step1_shippingType_1').removeClass('hidden');
            $('[for="checkout_step2_paymentType_1"]').removeClass('hidden');
            $('.checkout_step2_paymentType_4').addClass('hidden');
            $('[for="checkout_step2_paymentType_4"]').addClass('hidden');
        }
        $('#checkout_step2_paymentType_0').prop('checked', true);
        $('.checkout_step2_paymentType_0').removeClass('hidden');
        $('#total-cost').data('cost', totalCost.toFixed(2));
        let totalCostString = totalCost.toFixed(2);
        let newTotalCostString = totalCostString.replace('.', ',');
        $('#total-cost').html(newTotalCostString);
    });

    $('input[name="checkout_step2[paymentType]"]').on('change', function () {
        let i = 0;
        $('input[name="checkout_step2[paymentType]"]').each(function () {
            if ($('#checkout_step2_paymentType_' + i).is(':checked')) {
                $('.checkout_step2_paymentType_' + i).removeClass('hidden');
            } else {
                $('.checkout_step2_paymentType_' + i).addClass('hidden');
            }
            i = i + 1;
        })
        let totalCost = 0;
        let shippingCost = 0;
        let couponDisc = 0;
        let antikatavoliCost = 0;
        if ($('#coupon-discount').length > 0) {
            couponDisc = parseFloat($('#coupon-discount').data('discount'));
        }
        if ($('#checkout_step2_paymentType_4').is(':checked')) {
            if ($('#cart-cost').data('cost') - couponDisc <= 39) {
                $('#cart-subtotal-pay-on-delivery').removeClass('hidden');
                antikatavoliCost = 1.5;
                shippingCost = parseFloat($('#shipping-cost').data('cost'));
            }
            totalCost = $('#cart-cost').data('cost') + antikatavoliCost + shippingCost - couponDisc;
        } else {
            $('#cart-subtotal-pay-on-delivery').addClass('hidden');
            totalCost = $('#cart-cost').data('cost') + shippingCost - couponDisc;
        }
        $('#total-cost').data('cost', totalCost.toFixed(2));
        let totalCostString = totalCost.toFixed(2);
        let newTotalCostString = totalCostString.replace('.', ',');
        $('#total-cost').html(newTotalCostString);

    });

    $('#checkout_step1_series_0').attr('checked', 'checked');
    // $('#checkout_step4_paymentType_0').attr('checked', 'checked');
    initializePaymentInfos();
    calculateAntikatovoli();
    if ($('#checkout_step1_shippingType_1').is(':checked')) {
        $('[for="checkout_step2_paymentType_4"]').parent('div').addClass('hidden');
    }

    $('#use_same_address').on('click', function () {
        if ($('#use_same_address').is(':checked') === true) {
            $('#ship-address').removeClass('hidden');
        } else {
            $('#ship-address').addClass('hidden');
        }
    });

});

function initializePaymentInfos() {
    // $('#checkout_step1_shippingType_1').html('Για να δεσμευτεί μια παραγγελία έτσι ώστε να είναι δυνατή η παραλαβή της από το Φαρμακείο απαιτεί χρόνο τριών εως πέντε ωρών.\n' +
    //     'Μόλις η παραγγελία σας είναι διαθέσιμη στο κατάστημα, θα επικοινωνήσουμε μαζί σας τηλεφωνικά για να περάσετε να παραλάβετε.');
    // $('#checkout_step2_paymentType').css({'padding-top': '0'});
    // $('#checkout_step2_paymentType_0').removeClass('hidden');
    // $('#checkout_step2_paymentType_0').html('Παρακαλούμε καταθέστε το ακριβές τελικό ποσό της παραγγελίας σας στην Εθνική Τράπεζα, GR 11 0110 1740 0000 1744 0073 280 (Αρ. Λογαριασμού: 1744 0073 280), ή στην Τράπεζα Πειραιώς, GR 42 0172 0850 0050 8503 9845 834 (Αρ. Λογαριασμού: 5085 039845 834), ή στη Eurobank, GR 23 0260 0100 0004 8010 0400 234 (Αρ. Λογαριασμού: 0026.0010.48.0100400234).');
    // $('#checkout_step2_paymentType_1').html('Η πληρωμή θα γίνει με μετρητά κατά την παραλαβή από το κατάστημα.');
    // $('.checkout_step1_paymentType_2').html('Πληρώστε χρησιμοποιώντας την πιστωτική ή χρεωστική σας κάρτα');
    // $('.checkout_step1_paymentType_3').html('<img src="http://anosia.democloudon.cloud/public/images/paypal.png" alt="Λογότυπο Αποδοχής PayPal"><br><a href="https://www.paypal.com/gr/webapps/mpp/paypal-popup" target="_blank">Τι είναι το PayPal;</a> <br>Πληρώστε με ασφάλεια μέσω PayPal.');
    // $('.checkout_step1_paymentType_4').html('Έξοδα αντικαταβολής (1,5€) χρεώνονται μόνο σε παραγγελίες μικρότερες των 39€.\n' +
    //     'Για παραγγελίες μεγαλύτερες των 39€ δεν χρεώνονται ούτε έξοδα αντικαταβολής, ούτε μεταφορικά.');
    $('[for="checkout_step2_paymentType_1"]').addClass('hidden');

    $('.checkout_step1_shippingType_1').html('Για να δεσμευτεί μια παραγγελία έτσι ώστε να είναι δυνατή η παραλαβή της από το Φαρμακείο απαιτεί χρόνο τριών εως πέντε ωρών.\n' +
        'Μόλις η παραγγελία σας είναι διαθέσιμη στο κατάστημα, θα επικοινωνήσουμε μαζί σας τηλεφωνικά για να περάσετε να παραλάβετε.');
    $('#checkout_step2_paymentType').css({'padding-top': '0'});
    $('.checkout_step2_paymentType_0').removeClass('hidden');
    $('.checkout_step2_paymentType_0').html('Παρακαλούμε καταθέστε το ακριβές τελικό ποσό της παραγγελίας σε οποιοδήποτε από τους παρακάτω λογαριασμούς: <br>Εθνική Τράπεζα, GR1101101740000017440073280<br>Τράπεζα Πειραιώς, GR4201720850005085039845834<br>Eurobank, GR2302600100000480100400234.');
    $('.checkout_step2_paymentType_1').html('Η πληρωμή θα γίνει με μετρητά κατά την παραλαβή από το κατάστημα.');
    $('.checkout_step2_paymentType_2').html('Πληρώστε χρησιμοποιώντας την πιστωτική ή χρεωστική σας κάρτα');
    $('.checkout_step2_paymentType_3').html('<img width="51" style="width: 51px;" src="https://anosia.democloudon.cloud/public/images/paypal.png" alt="Λογότυπο Αποδοχής PayPal"><br><a href="https://www.paypal.com/gr/webapps/mpp/paypal-popup" target="_blank">Τι είναι το PayPal;</a> <br>Πληρώστε με ασφάλεια μέσω PayPal.');
    $('.checkout_step2_paymentType_4').html('Έξοδα αντικαταβολής (1,5€) χρεώνονται μόνο σε παραγγελίες μικρότερες των 39€.\n' +
        'Για παραγγελίες μεγαλύτερες των 39€ δεν χρεώνονται ούτε έξοδα αντικαταβολής, ούτε μεταφορικά.');
}

function calculateAntikatovoli() {
    let shippingCost = 0;
    let couponDisc = 0;
    let antikatavoliCost = 0;
    if ($('#coupon-discount').length > 0) {
        couponDisc = $('#coupon-discount').data('discount');
    }
    if ($('#cart-cost').data('cost') - couponDisc >= 39) {
        shippingCost = 0;
        $('#shipping-cost').html('0,00');
    } else {
        shippingCost = parseFloat($('#shipping-cost').data('cost'));
    }
    if ($('#checkout_step4_paymentType_4').is(':checked')) {
        antikatavoliCost = 1.5;
        $('#cart-subtotal-pay-on-delivery').removeClass('hidden');
    } else {
        $('#cart-subtotal-pay-on-delivery').addClass('hidden');
    }
    totalCost = $('#cart-cost').data('cost') + antikatavoliCost + shippingCost - couponDisc;
    $('#total-cost').data('cost', totalCost.toFixed(2));
    let totalCostString = totalCost.toFixed(2);
    let newTotalCostString = totalCostString.replace('.', ',');
    $('#total-cost').html(newTotalCostString);
}
