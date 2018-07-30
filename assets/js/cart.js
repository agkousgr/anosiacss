$('#update-cart-btn').on('click', function (e) {
    e.preventDefault();
    $('form[name="cart-items"]').submit();
})