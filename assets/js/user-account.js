$('#identity-link').on('click', function (e) {
    e.preventDefault();
    $('.register-form').toggleClass('hidden');
})
$('#address-link').on('click', function (e) {
    e.preventDefault();
    $('.address-form').toggleClass('hidden');
})
$('#history-link').on('click', function (e) {
    e.preventDefault();
    $('.orders-form').toggleClass('hidden');
})