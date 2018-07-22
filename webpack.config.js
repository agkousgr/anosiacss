var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('js/ie8-responsive-file-warning', './assets/js/ie8-responsive-file-warning.js')
    .addEntry('js/html5shiv.min', './assets/js/html5shiv.min.js')
    .addEntry('js/respond.min', './assets/js/respond.min.js')
    .addEntry('js/jquery.min', './assets/js/jquery.min.js')
    .addEntry('js/bootstrap', './assets/js/bootstrap.js')
    .addEntry('js/custom', './assets/js/custom.js')
    .addEntry('js/owl.carousel', './assets/js/owl.carousel.js')
    .addEntry('js/jquery-scrolltofixed-min', './assets/js/jquery-scrolltofixed-min.js')
    .addEntry('js/jquery.touchSwipe.min', './assets/js/jquery.touchSwipe.min.js')
    .addEntry('js/jquery.matchHeight-min', './assets/js/jquery.matchHeight-min.js')
    .addEntry('js/jquery.flashblue-plugins', './assets/js/jquery.flashblue-plugins.js')
    .addEntry('js/jquery.apex-slider', './assets/js/jquery.apex-slider.js')
    .addEntry('js/bootstrap-number-input', './assets/js/bootstrap-number-input.js')
    .addEntry('js/easyResponsiveTabs', './assets/js/easyResponsiveTabs.js')
    .addEntry('js/jquery.bxslider.min', './assets/js/jquery.bxslider.min.js')
    .addEntry('js/jquery.mCustomScrollbar.concat.min', './assets/js/jquery.mCustomScrollbar.concat.min.js')
    .addEntry('js/wow', './assets/js/wow.js')
    .addEntry('js/jquery-ui', './assets/js/jquery-ui.js')
    .addEntry('js/configuration', './assets/js/configuration.js')
    .addEntry('js/home-page', './assets/js/home-page.js')
    .addEntry('js/product-list', './assets/js/product-list.js')
    .addEntry('js/checkout', './assets/js/checkout.js')
    .addEntry('js/user-account', './assets/js/user-account.js')
    .addEntry('js/cart', './assets/js/cart.js')


    // .addStyleEntry('css/style', './assets/css/style.css')
    .addStyleEntry('css/apex-slider', './assets/css/apex-slider.css')
    .addStyleEntry('css/orders', './assets/css/orders.css')
    .addStyleEntry('css/user', './assets/css/user.css')

    .addLoader({
        test: /\.(png|jpg|svg)$/,
        loader: 'file-loader',
        options: {
            name: '/[name].[hash:7].[ext]',
            publicPath: '/build',
            outputPath: 'images'
        }
    })

    // uncomment if you use Sass/SCSS files
    // .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
