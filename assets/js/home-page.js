// $('body').startComponents();

$('.add-to-cart').on('click', function () {
    // alert($(this).data('id') + ' | ' + $(this).data('quantity'));
    $('#collapseCart').load(Routing.generate('add_to_cart', {'id': $(this).data('id')}));
});

$('.owl-dots').css('display', 'none');

// collapseCart

$(document).ready(function () {
    $('#collapseCart').load(Routing.generate('load_top_cart'));
});