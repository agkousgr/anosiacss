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