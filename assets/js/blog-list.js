$(document).ready(function () {
    $('.blog-img').hover(function () {
        $(this).find('img').addClass('transition');

    }, function () {
        $(this).find('img').removeClass('transition');
    });
});