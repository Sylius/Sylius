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

const assetsDir = path.dirname(require.resolve('@sylius/shop-bundle/package.json'));

const SYLIUS_THEMES_DIRS = [
    'src/Sylius/Bundle/ShopBundle/themes',
    'vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/themes',
    'vendor/sylius/shop-bundle/themes'
];

const THEME_BUNDLE_WEBPACK_PATH = 'vendor/sylius/theme-bundle/src/Resources/webpack';

class SyliusShop {
    /**
     * Provide a light Webpack configuration for Sylius Shop
     * All the stimulus stuff should be handled by the app.shop entrypoint
     */
    static getBaseWebpackConfig(rootDir) {
        this._prepareWebpackConfig(rootDir);
        Encore
            .addEntry('shop-entry', path.resolve(assetsDir, 'entrypoint.js'));

        const shopConfig = Encore.getWebpackConfig();

        shopConfig.externals = { ...shopConfig.externals, window: 'window', document: 'document' };
        shopConfig.name = 'shop';

        Encore.reset();

        return shopConfig;
    }

    /**
     * For a ready-to-use Stimulus bridge. Should be used only for sylius/sylius tests
     * For instances started with Sylius-Standard < 2.0.4, it'll still be used unless upgrading webpack.config.js
     * to use the method above getBaseWebpackConfig()
     */
    static getWebpackConfig(rootDir) {
        this._prepareWebpackConfig(rootDir);
        // For a ready-to-use Stimulus bridge. Should be used only for sylius/sylius tests
        Encore
            .addEntry('shop-entry', path.resolve(assetsDir, 'app.js'))
            .enableStimulusBridge(path.resolve(assetsDir, 'controllers.json'));
        const shopConfig = Encore.getWebpackConfig();

        shopConfig.externals = { ...shopConfig.externals, window: 'window', document: 'document' };
        shopConfig.name = 'shop';

        Encore.reset();

        return shopConfig;
    }

    static getThemesWebpackConfig(rootDir, themesDirs = ['themes'], scanDepth = 1) {
        const SyliusTheme = require(path.resolve(rootDir, THEME_BUNDLE_WEBPACK_PATH));

        const { entries, aliases } = SyliusTheme.getThemesAssets(rootDir, {
            directories: [...(Array.isArray(themesDirs) ? themesDirs : [themesDirs]), ...SYLIUS_THEMES_DIRS],
            scanDepth,
            assetsDir: 'assets/shop'
        });

        if (Object.keys(entries).length === 0) {
            return null;
        }

        Encore
            .setOutputPath('public/build/themes/shop/')
            .setPublicPath('/build/themes/shop')
            .disableSingleRuntimeChunk()
            .cleanupOutputBeforeBuild()
            .enableSourceMaps(!Encore.isProduction())
            .enableVersioning(Encore.isProduction())
            .enableSassLoader((options) => {
                // eslint-disable-next-line no-param-reassign
                options.additionalData = `$rootDir: '${rootDir}';`;
                // eslint-disable-next-line no-param-reassign
                options.sassOptions = { ...options.sassOptions, charset: false };
            })
            .addAliases(aliases)
            .addEntries(entries);

        const themesConfig = Encore.getWebpackConfig();

        themesConfig.externals = { ...themesConfig.externals, window: 'window', document: 'document' };
        themesConfig.name = 'shop.themes';

        Encore.reset();

        return themesConfig;
    }

    static _prepareWebpackConfig(rootDir) {
        Encore
            .setOutputPath('public/build/shop/')
            .setPublicPath('/build/shop')
            .disableSingleRuntimeChunk()
            .cleanupOutputBeforeBuild()
            .enableSourceMaps(!Encore.isProduction())
            .enableVersioning(Encore.isProduction())
            .enableSassLoader((options) => {
                // eslint-disable-next-line no-param-reassign
                options.additionalData = `$rootDir: '${rootDir}';`;
                // eslint-disable-next-line no-param-reassign
                options.sassOptions = { ...options.sassOptions, charset: false };
            });
    }
}

module.exports = SyliusShop;
