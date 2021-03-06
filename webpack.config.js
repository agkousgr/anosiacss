var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/assets/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/public/assets')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    // .addEntry('js/ie8-responsive-file-warning', './assets/js/ie8-responsive-file-warning.js')
    // .addEntry('js/html5shiv.min', './assets/js/html5shiv.min.js')
    // .addEntry('js/respond.min', './assets/js/respond.min.js')
    // .addEntry('js/jquery.min', './assets/js/jquery.min.js')
    .addEntry('js/bootstrap', './assets/js/bootstrap.js')
    .addEntry('js/custom', './assets/js/custom.js')
    // .addEntry('js/owl.carousel', './assets/js/owl.carousel.js')
    // .addEntry('js/jquery-scrolltofixed-min', './assets/js/jquery-scrolltofixed-min.js')
    .addEntry('js/jquery.touchSwipe.min', './assets/js/jquery.touchSwipe.min.js')
    .addEntry('js/jquery.matchHeight-min', './assets/js/jquery.matchHeight-min.js')
    // .addEntry('js/jquery.flashblue-plugins', './assets/js/jquery.flashblue-plugins.js')
    // .addEntry('js/jquery.apex-slider', './assets/js/jquery.apex-slider.js')
    // .addEntry('js/bootstrap-number-input', './assets/js/bootstrap-number-input.js')
    .addEntry('js/easyResponsiveTabs', './assets/js/easyResponsiveTabs.js')
    // .addEntry('js/jquery.bxslider.min', './assets/js/jquery.bxslider.min.js')
    // .addEntry('js/jquery.mCustomScrollbar.concat.min', './assets/js/jquery.mCustomScrollbar.concat.min.js')
    // .addEntry('js/wow', './assets/js/wow.js')
    // .addEntry('js/jquery-ui', './assets/js/jquery-ui.js')
    .addEntry('js/configuration', './assets/js/configuration.js')
    .addEntry('js/home-page', './assets/js/home-page.js')
    .addEntry('js/product-list', './assets/js/product-list.js')
    .addEntry('js/product-view', './assets/js/product-view.js')
    .addEntry('js/checkout', './assets/js/checkout.js')
    .addEntry('js/user-account', './assets/js/user-account.js')
    .addEntry('js/user-register', './assets/js/user-register.js')
    .addEntry('js/cart', './assets/js/cart.js')
    .addEntry('js/user-google-address', './assets/js/user-google-address.js')
    .addEntry('js/pireaus', './assets/js/pireaus.js')
    .addEntry('js/blog-list', './assets/js/blog-list.js')
    .addEntry('js/courier-tracker', './assets/js/courier-tracker.js')
    .addEntry('js/jquery.validate.min', './assets/js/jquery.validate.min.js')
    .addEntry('js/back-to-top', './assets/js/back-to-top.js')
    .addEntry('js/google-address', './assets/js/google-address.js')
    .addEntry('js/hide-top-cart', './assets/js/hide-top-cart.js')

    // Admin javascripts
    .addEntry('admin/js/bootstrap.min', './assets/admin/js/bootstrap.min.js')
    .addEntry('admin/js/detect', './assets/admin/js/detect.js')
    .addEntry('admin/js/fastclick', './assets/admin/js/fastclick.js')
    .addEntry('admin/js/jquery.blockUI', './assets/admin/js/jquery.blockUI.js')
    // .addEntry('admin/js/jquery.min', './assets/admin/js/jquery.min.js')
    .addEntry('admin/js/jquery.nicescroll', './assets/admin/js/jquery.nicescroll.js')
    .addEntry('admin/js/modernizr.min', './assets/admin/js/modernizr.min.js')
    .addEntry('admin/js/moment.min', './assets/admin/js/moment.min.js')
    .addEntry('admin/js/pikeadmin', './assets/admin/js/pikeadmin.js')
    .addEntry('admin/js/popper.min', './assets/admin/js/popper.min.js')
    .addEntry('admin/js/switchery.min', './assets/admin/js/switchery/switchery.min.js')
    .addEntry('admin/js/jquery.scrollTo.min', './assets/admin/js/jquery.scrollTo.min.js')
    .addEntry('admin/js/slider', './assets/admin/js/slider/slider.js')
    .addEntry('admin/js/our-corner', './assets/admin/js/home-page-modules/our-corner.js')
    .addEntry('admin/js/latest-offers', './assets/admin/js/home-page-modules/latest-offers.js')
    .addEntry('admin/js/jstree.min', './assets/admin/plugins/jstree/jstree.min.js')
    .addEntry('admin/js/category', './assets/admin/js/category/category.js')
    .addEntry('admin/js/article', './assets/admin/js/article/article.js')
    .addEntry('admin/js/article_form', './assets/admin/js/article/article_form.js')
    .addEntry('admin/js/category-slider', './assets/admin/js/category-slider/category-slider.js')
    .addEntry('admin/js/category-slider-list', './assets/admin/js/category-slider/category-slider-list.js')
    .addEntry('admin/js/promo-categories', './assets/admin/js/promo-categories/promo-categories.js')



    .addStyleEntry('css/jquery-ui', './assets/css/jquery-ui.css')
    .addStyleEntry('css/style', './assets/css/style.css')
    .addStyleEntry('css/apex-slider', './assets/css/apex-slider.css')
    .addStyleEntry('css/orders', './assets/css/orders.css')
    .addStyleEntry('css/user', './assets/css/user.css')
    .addStyleEntry('css/404', './assets/css/404.css')
    .addStyleEntry('css/ion.rangeSlider', './assets/plugins/ion-rangeslider/ion.rangeSlider.css')
    .addStyleEntry('css/ion.rangeSlider.skinModern', './assets/plugins/ion-rangeslider/ion.rangeSlider.skinModern.css')
    .addStyleEntry('css/home', './assets/css/home.css')

    // Admin stylesheets
    .addStyleEntry('admin/css/bootstrap.min', './assets/admin/css/bootstrap.min.css')
    .addStyleEntry('admin/css/style', './assets/admin/css/style.css')
    .addStyleEntry('admin/font-awesome/css/font-awesome.min', './assets/admin/font-awesome/css/font-awesome.min.css')
    .addStyleEntry('admin/css/switchery.min', './assets/admin/css/switchery/switchery.min.css')
    .addStyleEntry('admin/css/jstree', './assets/admin/plugins/jstree/style.css')
    .addStyleEntry('admin/css/category', './assets/admin/css/category/category.css')
    .addStyleEntry('admin/css/login/fonts.googleapis.com', './assets/admin/css/login/fonts.googleapis.com.css')
    .addStyleEntry('admin/css/login/bootstrap.min', './assets/admin/css/login/bootstrap.min.css')
    .addStyleEntry('admin/css/login/ace.min', './assets/admin/css/login/ace.min.css')
    .addStyleEntry('admin/css/login/ace-rtl.min', './assets/admin/css/login/ace-rtl.min.css')
    .addStyleEntry('admin/css/plugins/select2.min', './assets/admin/css/plugins/select2/select2.min.css')
    .addStyleEntry('admin/css/daterangepicker', './assets/admin/css/daterangepicker.css')

    // uncomment if you use Sass/SCSS files
    // .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    // .autoProvidejQuery()
    .autoProvideVariables({
        $: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery'
    })
;

module.exports = Encore.getWebpackConfig();
