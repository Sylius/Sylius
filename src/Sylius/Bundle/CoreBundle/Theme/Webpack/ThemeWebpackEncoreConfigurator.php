<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Theme\Webpack;

use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\FileExistenceResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ThemeWebpackEncoreConfigurator
{
    public static function configure(
        ContainerBuilder $container,
        string $themesDir,
        array $channels,
        string $entrypointPattern,
        string $buildNamePattern,
        string $outputPathPattern,
    ): void {
        $container->addResource(new FileExistenceResource($themesDir));

        if (!is_dir($themesDir)) {
            return;
        }

        $container->addResource(new DirectoryResource($themesDir, '/^[^.].*$/'));

        $builds = [];

        foreach (scandir($themesDir) ?: [] as $entry) {
            if ('.' === $entry || '..' === $entry) {
                continue;
            }

            $themePath = $themesDir . '/' . $entry;
            if (!is_dir($themePath)) {
                continue;
            }

            foreach ($channels as $channel) {
                $entrypoint = $themePath . '/' . self::resolve($entrypointPattern, $entry, $channel);
                if (!file_exists($entrypoint)) {
                    continue;
                }

                $buildName = self::resolve($buildNamePattern, $entry, $channel);
                $outputPath = self::resolve($outputPathPattern, $entry, $channel);

                $builds[$buildName] = $outputPath;
            }
        }

        if ([] === $builds) {
            return;
        }

        $container->prependExtensionConfig('webpack_encore', ['builds' => $builds]);
    }

    private static function resolve(string $pattern, string $code, string $channel): string
    {
        return str_replace(['{code}', '{channel}'], [$code, $channel], $pattern);
    }
}
