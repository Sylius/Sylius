<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ThemeBundle;

use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorAliasingPass;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorFallbackLocalesPass;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorLoaderProviderPass;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\TranslatorResourceProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SyliusThemeBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TranslatorAliasingPass());
        $container->addCompilerPass(new TranslatorFallbackLocalesPass());
        $container->addCompilerPass(new TranslatorLoaderProviderPass());
        $container->addCompilerPass(new TranslatorResourceProviderPass());
    }
}
