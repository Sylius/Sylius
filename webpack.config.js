const path = require('path');
const Encore = require('@symfony/webpack-encore');

const SyliusAdmin = require('@sylius-ui/admin');

const uiBundleScripts = path.resolve(__dirname, 'src/Sylius/Bundle/UiBundle/Resources/private/js/');
const uiBundleResources = path.resolve(__dirname, 'src/Sylius/Bundle/UiBundle/Resources/private/');

// Shop config
Encore
  .setOutputPath('public/build/shop/')
  .setPublicPath('/build/shop')
  .addEntry('shop-entry', './src/Sylius/Bundle/ShopBundle/Resources/private/entry.js')
  .disableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .enableSassLoader();

const shopConfig = Encore.getWebpackConfig();

shopConfig.resolve.alias['sylius/ui'] = uiBundleScripts;
shopConfig.resolve.alias['sylius/ui-resources'] = uiBundleResources;
shopConfig.name = 'shop';

Encore.reset();

const adminConfig = SyliusAdmin.getWebpackConfig(path.resolve(__dirname));

module.exports = [shopConfig, adminConfig];
