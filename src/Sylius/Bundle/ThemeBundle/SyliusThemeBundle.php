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

use Sylius\Bundle\ThemeBundle\DependencyInjection\Compiler\ThemeRepositoryPass;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\ThemeAwareLoaderDecoratorPass;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\ThemeAwareSourcesPass;
use Sylius\Bundle\ThemeBundle\Translation\DependencyInjection\Compiler\ThemesTranslationsSourcesPass;
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

        $container->addCompilerPass(new ThemeRepositoryPass());
        $container->addCompilerPass(new ThemeAwareSourcesPass());
        $container->addCompilerPass(new ThemeAwareLoaderDecoratorPass());
    }
}
