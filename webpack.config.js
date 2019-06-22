var Encore = require('@symfony/webpack-encore');

Encore
  .disableSingleRuntimeChunk()
  .setOutputPath('web/assets/')
  .setPublicPath('/assets')

  .addStyleEntry('main', './source/scss/main.scss')
  .addEntry('raffler', './source/js/raffler.js')

  .copyFiles({
    from: './source/images',
    to: 'images/[path][name].[hash:8].[ext]'
  })

  .enableSassLoader()
  .enableSourceMaps(!Encore.isProduction())
  .cleanupOutputBeforeBuild()
  .enableVersioning()
  .configureFilenames({
    js: '[name].[contenthash].js',
    css: '[name].[contenthash].css'
  })
;

module.exports = Encore.getWebpackConfig();
