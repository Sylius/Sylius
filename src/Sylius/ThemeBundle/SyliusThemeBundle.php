<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ThemeBundle;

use Sylius\ThemeBundle\Configuration\Filesystem\FilesystemConfigurationSourceFactory;
use Sylius\ThemeBundle\Configuration\Test\TestConfigurationSourceFactory;
use Sylius\ThemeBundle\DependencyInjection\SyliusThemeExtension;
use Sylius\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorAliasingPass;
use Sylius\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorFallbackLocalesPass;
use Sylius\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorLoaderProviderPass;
use Sylius\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorResourceProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SyliusThemeBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        /** @var SyliusThemeExtension $themeExtension */
        $themeExtension = $container->getExtension('sylius_theme');
        $themeExtension->addConfigurationSourceFactory(new FilesystemConfigurationSourceFactory());
        $themeExtension->addConfigurationSourceFactory(new TestConfigurationSourceFactory());

        $container->addCompilerPass(new TranslatorAliasingPass());
        $container->addCompilerPass(new TranslatorFallbackLocalesPass());
        $container->addCompilerPass(new TranslatorLoaderProviderPass());
        $container->addCompilerPass(new TranslatorResourceProviderPass());
    }
}
