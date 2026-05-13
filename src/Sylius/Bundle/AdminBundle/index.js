/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import path from 'path';
import { fileURLToPath } from 'url';
import Encore from '@symfony/webpack-encore';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

class SyliusAdmin {
    /**
     * Provide a light Webpack configuration for Sylius Admin
     * All the stimulus stuff should be handled by the app.admin entrypoint
     */
    static async getBaseWebpackConfig(rootDir) {
        this._prepareWebpackConfig(rootDir);

        Encore
            .addEntry('admin-entry', path.resolve(__dirname, 'Resources/assets/entrypoint.js'));

        const adminConfig = await Encore.getWebpackConfig();

        adminConfig.externals = { ...adminConfig.externals, window: 'window', document: 'document' };
        adminConfig.name = 'admin';

        Encore.reset();

        return adminConfig;
    }

    /**
     * For a ready-to-use Stimulus bridge. Should be used only for sylius/sylius tests
     * For instances started with Sylius-Standard < 2.0.4, it'll still be used unless upgrading webpack.config.js
     * to use the method above getBaseWebpackConfig()
     */
    static async getWebpackConfig(rootDir) {
        this._prepareWebpackConfig(rootDir);

        Encore
            .addEntry('admin-entry', path.resolve(__dirname, 'Resources/assets/app.js'))
            .enableStimulusBridge(path.resolve(__dirname, 'Resources/assets/controllers.json'));

        const adminConfig = await Encore.getWebpackConfig();

        adminConfig.externals = { ...adminConfig.externals, window: 'window', document: 'document' };
        adminConfig.name = 'admin';

        Encore.reset();

        return adminConfig;
    }

    static _prepareWebpackConfig(rootDir) {
        Encore
            .setOutputPath('public/build/admin/')
            .setPublicPath('/build/admin')
            .addEntry('admin-product-entry', path.resolve(__dirname, 'Resources/assets/product-entrypoint.js'))
            .disableSingleRuntimeChunk()
            .cleanupOutputBeforeBuild()
            .enableSourceMaps(!Encore.isProduction())
            .enableVersioning(Encore.isProduction())
            .enableSassLoader((options) => {
                // eslint-disable-next-line no-param-reassign
                options.additionalData = `$rootDir: '${rootDir}';`;
            });
    }
}

export default SyliusAdmin;
