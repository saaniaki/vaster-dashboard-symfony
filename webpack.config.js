var Encore = require('@symfony/webpack-encore');

Encore
    // directory where all compiled assets will be stored
    .setOutputPath('web/build/')

    // what's the public path to this directory (relative to your project's document root dir)
    .setPublicPath('/build')

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // will output as web/build/app.js
    //.addEntry('app', './assets/js/main.js')

    .addEntry('dashboard', ['./assets/js/main.js', './assets/js/dashboard.js'])
    .addEntry('pages', ['./assets/js/main.js', './assets/js/pages.js'])
    .addEntry('admin', ['./assets/js/main.js', './assets/js/admin.js'])
    .addEntry('module', ['./assets/js/main.js', './assets/js/module.js'])

    // will output as web/build/global.css
    .addStyleEntry('global', './assets/css/global.scss')

    // allow sass/scss files to be processed
    .enableSassLoader()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .autoProvideVariables({
        Moment: 'moment'
    })

    .enableSourceMaps(!Encore.isProduction())

    // create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning()


;

// export the final configuration
module.exports = Encore.getWebpackConfig();
