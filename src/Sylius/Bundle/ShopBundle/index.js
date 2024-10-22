/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

const path = require('path');
const Encore = require('@symfony/webpack-encore');

class SyliusShop {
  static getWebpackConfig(rootDir) {
    Encore
      .setOutputPath('public/build/shop/')
      .setPublicPath('/build/shop')
      .addEntry('shop-entry', path.resolve(__dirname, 'Resources/assets/entrypoint.js'))
      .disableSingleRuntimeChunk()
      .cleanupOutputBeforeBuild()
      .enableSourceMaps(!Encore.isProduction())
      .enableVersioning(Encore.isProduction())
      .enableSassLoader((options) => {
        // eslint-disable-next-line no-param-reassign
        options.additionalData = `$rootDir: ${rootDir};`;
      })
      .enableStimulusBridge(path.resolve(__dirname, 'Resources/assets/controllers.json'));

    const shopConfig = Encore.getWebpackConfig();

    shopConfig.externals = { ...shopConfig.externals, window: 'window', document: 'document' };
    shopConfig.name = 'shop';

    Encore.reset();

    return shopConfig;
  }
}

module.exports = SyliusShop;
