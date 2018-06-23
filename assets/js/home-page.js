// $('body').startComponents();

$('.add-to-cart').on('click', function () {
    alert($(this).data('id') + ' | ' + $(this).data('quantity'));
    $('#collapseCart').load(Routing.generate('add_to_cart', {'id': $(this).data('id')}));
});


// product view add to cart
$('.add-to-cart-view').on('click', function (e) {
    e.preventDefault();
    alert($(this).data('id') + ' | ' + $('#add-quantity').val());
    $('#collapseCart').load(Routing.generate('add_to_cart', {'id': $(this).data('id'),'quantity': $('#add-quantity').val()}));
});

$('.owl-dots').css('display', 'none');

// collapseCart

$(document).ready(function () {
    $('#collapseCart').load(Routing.generate('load_top_cart'));
});