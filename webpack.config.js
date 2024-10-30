const path = require('path');
const Encore = require('@symfony/webpack-encore');

const SyliusAdmin = require('@sylius-ui/admin');
const SyliusShop = require('@sylius-ui/shop');

const adminConfig = SyliusAdmin.getWebpackConfig(path.resolve(__dirname));

Encore
  .setOutputPath('public/build/shop/')
  .copyFiles({
    from: 'src/Sylius/Bundle/UiBundle/Resources/private/img/',
    to: 'images/[name].[ext]',
    pattern: /\.(png|jpg|jpeg)$/
  });

const shopConfig = SyliusShop.getWebpackConfig(path.resolve(__dirname));

module.exports = [adminConfig, shopConfig];
