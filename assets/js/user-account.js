$('#identity-link').on('click', function (e) {
    e.preventDefault();
    $('.register-form').toggleClass('hidden');
    $('.address-form').addClass('hidden');
    $('.orders-form').addClass('hidden');
})
$('#address-link').on('click', function (e) {
    e.preventDefault();
    $('.address-form').toggleClass('hidden');
    $('.register-form').addClass('hidden');
    $('.orders-form').addClass('hidden');
})
$('#history-link').on('click', function (e) {
    e.preventDefault();
    $('.orders-form').toggleClass('hidden');
    $('.register-form').addClass('hidden');
    $('.address-form').addClass('hidden');
})

$('#edit-main-address').on('click', function (e) {
    e.preventDefault();
    $('form[name="user_address"]').toggleClass('hidden');
    $('#view-main-address').toggleClass('hidden');
    $('.extra-addresses').toggleClass('hidden');
    $('.addresses-footer').toggleClass('hidden');
})

$(document).ready(function () {
   if ($('#view-main-address').hasClass('hidden') === false) {
       $('form[name="user_address"]').addClass('hidden');
   }
});