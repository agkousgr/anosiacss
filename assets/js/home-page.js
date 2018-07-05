// $('body').startComponents();

$('.add-to-cart').on('click', function () {
    // alert($(this).data('id'));
    $('#collapseCart').load(Routing.generate('add_to_cart', {'id': $(this).data('id'),'quantity': 1}));
});


// product view add to cart
$('.add-to-cart-view').on('click', function (e) {
    e.preventDefault();
    // alert($(this).data('id') + ' | ' + $('#add-quantity').val());
    $('#collapseCart').load(Routing.generate('add_to_cart', {'id': $(this).data('id'),'quantity': $('#add-quantity').val()}));
});


$('.owl-dots').css('display', 'none');

// collapseCart

$(document).ready(function () {
    $('#collapseCart').load(Routing.generate('load_top_cart'));
});

// WISH LIST
$('.add-to-wishlist').on('click', function () {
    e.preventDefault();
    alert($(this).data('id'));
    $('#collapseCart').load(Routing.generate('add_to_wishlist', {'id': $(this).data('id')}));
});