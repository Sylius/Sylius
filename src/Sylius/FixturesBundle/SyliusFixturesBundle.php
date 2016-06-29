<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\FixturesBundle;

use Sylius\FixturesBundle\DependencyInjection\Compiler\FixtureRegistryPass;
use Sylius\FixturesBundle\DependencyInjection\Compiler\ListenerRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SyliusFixturesBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new FixtureRegistryPass());
        $container->addCompilerPass(new ListenerRegistryPass());
    }
}
