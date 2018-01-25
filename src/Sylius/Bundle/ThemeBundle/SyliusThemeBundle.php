<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle;

use Sylius\Bundle\ThemeBundle\Configuration\Filesystem\FilesystemConfigurationSourceFactory;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestConfigurationSourceFactory;
use Sylius\Bundle\ThemeBundle\DependencyInjection\SyliusThemeExtension;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorFallbackLocalesPass;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorLoaderProviderPass;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorResourceProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusThemeBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        /** @var SyliusThemeExtension $themeExtension */
        $themeExtension = $container->getExtension('sylius_theme');
        $themeExtension->addConfigurationSourceFactory(new FilesystemConfigurationSourceFactory());
        $themeExtension->addConfigurationSourceFactory(new TestConfigurationSourceFactory());

        $container->addCompilerPass(new TranslatorFallbackLocalesPass());
        $container->addCompilerPass(new TranslatorLoaderProviderPass());
        $container->addCompilerPass(new TranslatorResourceProviderPass());
    }
}
