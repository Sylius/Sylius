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

namespace Sylius\Bundle\FixturesBundle;

use Sylius\Bundle\FixturesBundle\DependencyInjection\Compiler\FixtureRegistryPass;
use Sylius\Bundle\FixturesBundle\DependencyInjection\Compiler\ListenerRegistryPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusFixturesBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new FixtureRegistryPass());
        $container->addCompilerPass(new ListenerRegistryPass());
    }
}
