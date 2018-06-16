$('.stepper').bootstrapNumber();

$('#header').scrollToFixed();

$('.coleql_height').matchHeight();

$("#blue-mobile-menu").blueMobileMenu();

wow = new WOW(
    {
        animateClass: 'animated',
        offset: 100,
        callback: function(box) {
            console.log("WOW: animating <" + box.tagName.toLowerCase() + ">")
        }
    }
);
wow.init();
document.getElementById('moar').onclick = function() {
    var section = document.createElement('section');
//section.className = 'section--purple wow fadeInDown';
    this.parentNode.insertBefore(section, this);
};

$(".carousel").swipe({
    swipe: function(event, direction, distance, duration, fingerCount, fingerData) {
        if (direction == 'left') $(this).carousel('next');
        if (direction == 'right') $(this).carousel('prev');
    },
    allowPageScroll:"vertical"
});

$(function() {
    $( "#slider-range" ).slider({
        range: true,
        min: 100,
        max: 1000,
        values: [ 10, 1000 ],
        slide: function( event, ui ) {
            $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
        }
    });
    $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
        " - $" + $( "#slider-range" ).slider( "values", 1 ) );
});

$('body').startComponents();