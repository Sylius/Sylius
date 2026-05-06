/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

const path = require('path');
const fs = require('fs');
const Encore = require('@symfony/webpack-encore');

const DEFAULT_OPTIONS = {
    themesDir: 'themes',
    entrypoint: 'assets/{channel}/entrypoint.js',
    buildName: 'theme.{code}.{channel}',
    outputPath: 'public/build/themes/{code}/{channel}',
    publicPath: '/build/themes/{code}/{channel}'
};

class SyliusCore {
    static getThemesWebpackConfigs(rootDir, options = {}) {
        if (!options.channel) {
            throw new Error('SyliusCore.getThemesWebpackConfigs: "channel" option is required.');
        }

        const config = { ...DEFAULT_OPTIONS, ...options };
        if (!config.entryName) {
            config.entryName = `${config.channel}-entry`;
        }

        if (!config.buildName.includes('{code}')) {
            throw new Error(`SyliusCore.getThemesWebpackConfigs: "buildName" pattern must contain "{code}", got "${config.buildName}".`);
        }
        if (!config.outputPath.includes('{code}')) {
            throw new Error(`SyliusCore.getThemesWebpackConfigs: "outputPath" pattern must contain "{code}", got "${config.outputPath}".`);
        }

        const themesDir = path.isAbsolute(config.themesDir)
            ? config.themesDir
            : path.resolve(rootDir, config.themesDir);

        if (!fs.existsSync(themesDir) || !fs.statSync(themesDir).isDirectory()) return [];

        const configs = [];

        for (const themeCode of fs.readdirSync(themesDir).sort()) {
            if (themeCode.startsWith('.')) continue;

            const themePath = path.join(themesDir, themeCode);
            if (!fs.statSync(themePath).isDirectory()) continue;

            const entrypointRelative = SyliusCore._resolve(config.entrypoint, themeCode, config.channel);
            const entry = path.join(themePath, entrypointRelative);
            if (!fs.existsSync(entry)) continue;

            const buildName = SyliusCore._resolve(config.buildName, themeCode, config.channel);
            const outputPath = SyliusCore._resolve(config.outputPath, themeCode, config.channel);
            const publicPath = SyliusCore._resolve(config.publicPath, themeCode, config.channel);

            Encore.reset();

            Encore
                .setOutputPath(outputPath)
                .setPublicPath(publicPath)
                .addEntry(config.entryName, entry)
                .disableSingleRuntimeChunk()
                .cleanupOutputBeforeBuild()
                .enableSourceMaps(!Encore.isProduction())
                .enableVersioning(Encore.isProduction())
                .enableSassLoader((sassOptions) => {
                    // eslint-disable-next-line no-param-reassign
                    sassOptions.additionalData = `$rootDir: '${rootDir}';`;
                });

            const imagesDir = path.join(themePath, path.dirname(entrypointRelative), 'images');
            if (fs.existsSync(imagesDir)) {
                Encore.copyFiles({
                    from: imagesDir,
                    to: 'images/[path][name].[hash:8].[ext]',
                    pattern: /\.(png|jpe?g|gif|svg|webp)$/
                });
            }

            const themeConfig = Encore.getWebpackConfig();
            themeConfig.externals = { ...themeConfig.externals, window: 'window', document: 'document' };
            themeConfig.name = buildName;

            configs.push(themeConfig);
        }

        Encore.reset();

        return configs;
    }

    static _resolve(pattern, code, channel) {
        return pattern.replace(/\{code\}/g, code).replace(/\{channel\}/g, channel);
    }
}

module.exports = SyliusCore;
