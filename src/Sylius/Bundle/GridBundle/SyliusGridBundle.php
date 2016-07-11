<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle;

use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterDriversPass;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterFieldTypesPass;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterFiltersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Sylius\Component\Registry\DependencyInjection\Compiler\AliasedServicePass;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusGridBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AliasedServicePass(
            'sylius.registry.grid_driver',
            'sylius.grid_driver'
        ));

        $container->addCompilerPass(new AliasedServicePass(
            'sylius.registry.grid_field',
            'sylius.grid_field',
            'type'
        ));

        $container->addCompilerPass(new AliasedServicePass(
            'sylius.registry.grid_filter',
            'sylius.grid_filter',
            'type'
        ));
    }
}
