let Encore = require('@symfony/webpack-encore');
const PurgecssPlugin = require('purgecss-webpack-plugin');
const glob = require('glob-all');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('js/app', [

        './assets/js/app.js',
    ])
    .addStyleEntry('css/app', [
        './assets/css/app.scss'
    ])
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableVersioning()
    .enableSassLoader()
    .enablePostCssLoader()
    .enableIntegrityHashes()
    .autoProvidejQuery()
    .enableVueLoader()
    .addPlugin(new PurgecssPlugin({
        paths: glob.sync([
            'templates/**/*.html.twig'

        ]),
        extractors: [
            {
                extractor: class {
                    static extract(content) {
                        return content.match(/[A-z0-9-:\/]+/g) || []
                    }
                },
                extensions: ['twig']
            }
        ]
    }))
;

module.exports = Encore.getWebpackConfig();
