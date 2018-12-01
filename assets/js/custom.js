// JavaScript Document
require('webpack-jquery-ui/autocomplete');
require('owl.carousel');
require('select2');

let $mainWrapper = $('#mainWrapper');
$mainWrapper.on('show.bs.collapse', '.collapse', function () {
    $mainWrapper.find('.collapse.in').collapse('hide');
});

$(document).ready(function () {
    $("#keyword").autocomplete({
        source: Routing.generate('search'),
        select: function (event, ui) {
            event.preventDefault();
            $("#keyword").val(ui.item.label);
            // let url = Routing.generate('search');
            // window.location.assign(url);
        },
        focus: function (event, ui) {
            event.preventDefault();
            $("#keyword").val(ui.item.label);
        },
        minLength: 3
    });

    $("#keyword").keypress(function (e) {
        if (e.which == 13) {
            let data = {
                'term': $(this).val()
            };
            let url = Routing.generate('search', data);
            window.location.assign(url);
        }
    });

    $("#search-button").on('click', function (e) {
        e.preventDefault();
        let data = {
            'term': $("#keyword").val()
        };
        let url = Routing.generate('search', data);
        window.location.assign(url);
    });


    $(".dropdown").hover(
        function () {
            $('.dropdown-menu', this).stop(true, true).fadeToggle("500");
            $(this).toggleClass('open');
        },
        function () {
            $('.dropdown-menu', this).stop(true, true).fadeToggle("500");
            $(this).toggleClass('open');
        }
    );
});


// MENU TOGGLE
$("#menu-close").click(function (e) {
    e.preventDefault();
    $("#sidebar-wrapper").toggleClass("active");
});
$("#menu-cart-close").click(function (e) {
    e.preventDefault();
    $("#sidebar-cart-wrapper").toggleClass("active");
});
// Opens the sidebar menu
$("#menu-toggle").click(function (e) {
    e.preventDefault();
    $("#sidebar-wrapper").toggleClass("active");
});


(function ($) {
    $.fn.blueMobileMenu = function (options) {
        let settings = $.extend({
            color: "#556b2f",
            backgroundColor: "white"
        }, options);
        let id = this.attr("id");
        let mobile = true;
        $("a[href$=" + id + "]").addClass("blueMobileMenuIcon").on("click", function () {
            $("#" + id).slideToggle();
        });
        this.addClass("blueMobileMenu");
        this.find("li").attr("class", "firstLevel");
        this.find("li ul li").attr("class", "secondLevel");
        this.find("li ul li ul li").attr("class", "thirdLevel");
        this.find("li ul li ul li ul li").attr("class", "fourthLevel");
        this.find("li").has("ul").addClass("closed").prepend("<div class='icon_menu'></div>");
        if (mobile === true) {
            this.on("click", ".icon_menu", function (e) {
                $(this).parent().find("ul").first().slideToggle();
                if ($(this).parent().hasClass("closed")) {
//$(this).attr("src", "fa fa-chevron-down");
                    $(this).parent().removeClass("closed").addClass("open");
                } else {
                    $(this).parent().removeClass("open").addClass("closed");
                }
                e.stopPropagation();
            });
        }
        return this;
    }
}(jQuery));

$(document).ready(function () {
    let owl = $('.workCarousel');
    owl.owlCarousel({
        margin: 0,
        nav: true,
        loop: true,
        responsive: {
            0: {items: 1},
            480: {items: 2},
            768: {items: 3},
            992: {items: 4},
            1200: {items: 5}
        }
    })
})

$(document).ready(function () {
    let owl = $('.categoryCarousel');
    owl.owlCarousel({
        margin: 0,
        nav: false,
        loop: false,
        responsive: {
            0: {items: 1},
            480: {items: 2},
            768: {items: 4},
            992: {items: 5},
            1200: {items: 5}
        }
    })
})

$(document).ready(function () {
    let owl = $('.ProductScroll');
    owl.owlCarousel({
        margin: 30,
        nav: true,
        loop: false,
        responsive: {
            0: {items: 1},
            480: {items: 2},
            768: {items: 2},
            992: {items: 2},
            1200: {items: 2}
        }
    })
})

$(document).ready(function () {
    let owl = $('.ProductRelatedScroll');
    owl.owlCarousel({
        margin: 30,
        nav: true,
        loop: false,
        responsive: {
            0: {items: 1},
            480: {items: 2},
            768: {items: 2},
            992: {items: 3},
            1200: {items: 4}
        }
    })
})

$(document).ready(function () {
    let owl = $('.CartScroll');
    owl.owlCarousel({
        margin: 30,
        nav: true,
        loop: false,
        responsive: {
            0: {items: 1},
            480: {items: 2},
            768: {items: 2},
            992: {items: 3},
            1200: {items: 4}
        }
    })
})

$(document).ready(function () {
    let owl = $('.ProductScrollTab');
    owl.owlCarousel({
        margin: 30,
        nav: true,
        loop: true,
        responsive: {
            0: {items: 1},
            480: {items: 2},
            768: {items: 2},
            992: {items: 2},
            1200: {items: 3}
        }
    })
})


$(document).ready(function () {
    let owl = $('.ProductScrollMedia');
    owl.owlCarousel({
        margin: 30,
        nav: false,
        loop: true,
        responsive: {
            0: {items: 1},
            480: {items: 2},
            768: {items: 2},
            992: {items: 1},
            1200: {items: 1}
        }
    })
})

$(document).ready(function () {
    let owl = $('.OfferCarousel');
    owl.owlCarousel({
        margin: 30,
        nav: true,
        loop: true,
        responsive: {
            0: {items: 1},
            480: {items: 1},
            768: {items: 1},
            992: {items: 3},
            1200: {items: 3}
        }
    })
})

$(document).ready(function () {
    let owl = $('.CatCarousel');
    owl.owlCarousel({
        margin: 30,
        nav: true,
        loop: true,
        responsive: {
            0: {items: 1},
            480: {items: 2},
            768: {items: 4},
            992: {items: 5},
            1200: {items: 6}
        }
    })
})

$(document).ready(function () {
    let owl = $('#bx-pager');
    owl.owlCarousel({
        margin: 10,
        nav: true,
        loop: false,
        autoplay: false,
        responsive: {
            0: {items: 3},
            640: {items: 4},
            768: {items: 3},
            992: {items: 4},
        }
    })
})


$('.carousel-inner').each(function () {
    if ($(this).children('div').length === 1) $(this).siblings('.carousel-control, .carousel-indicators').hide();
});

$(document).ready(function () {
    $('#list').click(function (event) {
        event.preventDefault();
        $('#products .item').addClass('list-item');
    });
    $('#grid').click(function (event) {
        event.preventDefault();
        $('#products .item').removeClass('list-item');
        $('#products .item').addClass('grid-group-item');
    });
});

$("#blue-mobile-menu").blueMobileMenu();

$(document).ready(function () {
    // $('#nl-gender').select2();

    let cartContainer = $('#collapseCart');

    cartContainer.on('click', '.remove-item', function (e) {
        e.preventDefault();
        let data = {
            'id': $(this).data('id'),
            'name': $(this).data('name')
        }
        $.post(Routing.generate('delete_top_cart_item'), data, function (result) {
            if (result.success) {
                if (result.totalCartItems == 0) {
                    $('#cart-total-items').css('display', 'none');
                }
                $('#cart-total-items').html(result.totalCartItems);
                $('#collapseCart').load(Routing.generate('load_top_cart'));
            } else {
                swal({
                    title: 'Ουπς',
                    html: '<div style="font-size:17px;">Κάποια σφάλμα παρουσιάστηκε!</div>',
                    type: 'error',
                    timer: 5000
                });
            }
        });
    });

    $('#collapseCart').load(Routing.generate('load_top_cart'));
    if ($('input[name="cart"]').val() == 1) {
        $('#top-cart').css('display', 'none');
    }

    // NEWSLETTER REGISTRATION
    $('#newsletter-chk').on('click', function () {
        if ($('#newsletter-btn').is(':disabled') && $(this).is(':checked')) {
            $('#newsletter-btn').removeAttr('disabled');
        } else {
            $('#newsletter-btn').attr('disabled', 'disabled');
        }
    });

    $('#newsletter-btn').on('click', function () {
        let email = $('#newsletter-email').val();
        let name = $('#newsletter-name').val();
        let gender = $('#nl-gender').val();
        let age = $('#age').val();

        if (name === "") {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Παρακαλώ συμπληρώστε το ονοματεπώνυμό σας!</div>',
                type: 'error',
                timer: 5000
            });
        } else if (email === '') {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Παρακαλώ συμπληρώστε το email σας!</div>',
                type: 'error',
                timer: 5000
            });
        } else if (!isValidEmailAddress(email)) {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Παρακαλώ συμπληρώστε ένα έγκυρο email!</div>',
                type: 'error',
                timer: 5000
            });
        } else if (gender === '') {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Παρακαλώ συμπληρώστε το φύλο σας!</div>',
                type: 'error',
                timer: 5000
            });
        } else if (age === '') {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Παρακαλώ συμπληρώστε την ηλικία σας!</div>',
                type: 'error',
                timer: 5000
            });
        } else if ($('#newsletter-chk').is(':checked') !== true) {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Παρακαλώ τσεκάρετε την επιλογή "Επιθυμώ να ενημερώνομαι για προσφορές & νέα"!</div>',
                type: 'error',
                timer: 5000
            });
        } else {
            let data = {
                'email': email,
                'name': name,
                'gender': gender,
                'age': age
            }
            $.post(Routing.generate('newsletter_registration'), data, function (result) {
                if (result.success && result.exist === false) {
                    swal({
                        title: 'Newsletter',
                        html: '<div style="font-size:17px;">Η εγγραφή σας στο Newsletter έγινε με επιτυχία!</div>',
                        type: 'success',
                        timer: 5000
                    });
                    $('#newsletter-email').val('');
                    $('#newsletter-name').val('');
                    $('#nl-gender').val('');
                    $('#nl-age').val('');
                    $('#newsletter-btn').attr('disabled', 'disabled');
                    $('#newsletter-chk').removeAttr('checked');
                    // $('#collapseCart').load(Routing.generate('load_top_wishlist'));
                } else if (result.success === 'empty') {
                    swal({
                        title: 'Ουπς',
                        html: '<div style="font-size:17px;">Παρακαλώ συμπληρώστε τα στοιχεία σας!</div>',
                        type: 'error',
                        timer: 5000
                    });
                } else if (result.success && result.exist === true) {
                    swal({
                        title: 'Ουπς',
                        html: '<div style="font-size:17px;">To email σας βρίσκεται ήδη στη λίστα μας!</div>',
                        type: 'info',
                        timer: 5000
                    });
                } else {
                    swal({
                        title: 'Ουπς',
                        html: '<div style="font-size:17px;">Κάποια σφάλμα παρουσιάστηκε!</div>',
                        type: 'error',
                        timer: 5000
                    });
                }
            });
        }
    });
});

$('.add-to-wishlist').on('click', function () {
    let data = {
        'id': $(this).data('id'),
        'name': $(this).data('name')
    }
    $.post(Routing.generate('add_to_wishlist'), data, function (result) {
        if (result.success && result.exist === false) {
            swal({
                title: 'Wishlist',
                html: '<div style="font-size:17px;">Το προϊόν ' + result.prName + ' προστέθηκε με επιτυχία στη wislish σας!</div>',
                type: 'success',
                timer: 5000
            });
            $('#wishlist-total-items').css('display', 'inline');
            $('#wishlist-total-items').html(result.totalWishlistItems);
            // $('#collapseCart').load(Routing.generate('load_top_wishlist'));
        } else if (result.success && result.exist === true) {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Το προϊόν ' + result.prName + ' υπάρχει ήδη στη wislish σας!</div>',
                type: 'info',
                timer: 5000
            });
        } else {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Κάποια σφάλμα παρουσιάστηκε!</div>',
                type: 'error',
                timer: 5000
            });
        }
    });
})

// product view add to cart
$('.add-to-cart-view').on('click', function (e) {
    e.preventDefault();
    // alert($(this).data('id') + ' | ' + $('#add-quantity').val());
    $('#collapseCart').load(Routing.generate('add_to_cart', {
        'id': $(this).data('id'),
        'quantity': $('#add-quantity').val()
    }));
});


$('.add-to-cart').on('click', function (e) {
    e.preventDefault();
    let quantity = 1;
    if ($('#add-quantity').val()) {
        quantity = $('#add-quantity').val();
    } else {
        quantity = 1;
    }
    let data = {
        'id': $(this).data('id'),
        'quantity': quantity,
        'name': $(this).data('name')
    }
    $.post(Routing.generate('add_to_cart'), data, function (result) {
        if (result.success && result.exist === false) {
            // swal({
            //     title: 'Καλάθι',
            //     html: '<div style="font-size:17px;">Το προϊόν ' + result.prName + ' προστέθηκε με επιτυχία!</div>',
            //     type: 'success',
            //     timer: 5000
            // });
            $('#cart-total-items').css('display', 'inline');
            $('#cart-total-items').html(result.totalCartItems);
            $("#sidebar-cart-wrapper").toggleClass("active");
            $('#collapseCart').load(Routing.generate('load_top_cart'));
            setTimeout(function () {
                $("#sidebar-cart-wrapper").toggleClass("active")
            }, 3000);
        } else if (result.success && result.exist === true) {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Το προϊόν ' + result.prName + ' υπάρχει ήδη στο καλάθι σας!</div>',
                type: 'info',
                timer: 5000
            });
        } else {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Κάποια σφάλμα παρουσιάστηκε!</div>',
                type: 'error',
                timer: 5000
            });
        }
    });
});

$('.add-to-cart-inner').on('click', function (e) {
    e.preventDefault();
    let quantity = 1;
    let data = {
        'id': $(this).data('id'),
        'quantity': quantity,
        'name': $(this).data('name')
    }
    $.post(Routing.generate('add_to_cart'), data, function (result) {
        if (result.success && result.exist === false) {
            location.reload();
        } else if (result.success && result.exist === true) {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Το προϊόν ' + result.prName + ' υπάρχει ήδη στο καλάθι σας!</div>',
                type: 'info',
                timer: 5000
            });
        } else {
            swal({
                title: 'Ουπς',
                html: '<div style="font-size:17px;">Κάποια σφάλμα παρουσιάστηκε!</div>',
                type: 'error',
                timer: 5000
            });
        }
    });
});

$('.owl-dots').css('display', 'none');

$(".toggle-sidebar").click(function (e) {
    e.preventDefault();
    $("#sidebar-wrapper").toggleClass("active");
});

$(".toggle-cart-sidebar").click(function (e) {
    e.preventDefault();
    $("#sidebar-cart-wrapper").toggleClass("active");
});


// LOGIN
$('.btn-signin').on('click', function (e) {
    e.preventDefault();
    let data = {
        'username': $('input[name="_username"]').val(),
        'password': $('input[name="_password"]').val(),
    }
    if (data.username == '' || data.password == '') {
        swal({
            title: 'Είσοδος χρήστη',
            html: '<div style="font-size:17px;">Συμπληρώστε τα πεδία και δοκιμάστε ξανά!</div>',
            type: 'error',
            timer: 10000
        });
    } else {
        $.post(Routing.generate('login'), data, function (result) {
            if (result.success) {
                swal({
                    title: 'Είσοδος χρήστη',
                    html: '<div style="font-size:17px;">Συνδεθήκατε με επιτυχία!</div>',
                    type: 'success',
                    timer: 10000
                });
                location.reload();
            } else {
                swal({
                    title: 'Είσοδος χρήστη',
                    html: '<div style="font-size:17px;">' + result.errorMsg + '</div>',
                    type: 'error',
                    timer: 10000
                });
            }
        });
    }
});

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^(("[\w-+\s]+")|([\w-+]+(?:\.[\w-+]+)*)|("[\w-+\s]+")([\w-+]+(?:\.[\w-+]+)*))(@((?:[\w-+]+\.)*\w[\w-+]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][\d]\.|1[\d]{2}\.|[\d]{1,2}\.))((25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\.){2}(25[0-5]|2[0-4][\d]|1[\d]{2}|[\d]{1,2})\]?$)/i);
    return pattern.test(emailAddress);
};