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

class SyliusAdmin {
  static getWebpackConfig(rootDir) {
    const uiBundleScripts = path.resolve(__dirname, '../UiBundle/Resources/private/js/');
    const uiBundleResources = path.resolve(__dirname, '../UiBundle/Resources/private/');

    Encore
      .setOutputPath('public/build/admin/')
      .setPublicPath('/build/admin')
      .addEntry('admin-entry', path.resolve(__dirname, 'Resources/assets/entrypoint.js'))
      .addEntry('admin-product-entry', path.resolve(__dirname, 'Resources/assets/product-entrypoint.js'))
      .disableSingleRuntimeChunk()
      .cleanupOutputBeforeBuild()
      .enableSourceMaps(!Encore.isProduction())
      .enableVersioning(Encore.isProduction())
      .enableSassLoader((options) => {
        // eslint-disable-next-line no-param-reassign
        options.additionalData = `$rootDir: ${rootDir};`;
      })
      .enableStimulusBridge(path.resolve(__dirname, 'Resources/assets/controllers.json'));

    const adminConfig = Encore.getWebpackConfig();

    adminConfig.resolve.alias['sylius/ui'] = uiBundleScripts;
    adminConfig.resolve.alias['sylius/ui-resources'] = uiBundleResources;
    adminConfig.externals = { ...adminConfig.externals, window: 'window', document: 'document' };
    adminConfig.name = 'admin';

    Encore.reset();

    return adminConfig;
  }
}

module.exports = SyliusAdmin;
