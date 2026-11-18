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

namespace Sylius\Bundle\ShopBundle\DependencyInjection\Compiler;

use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ResettableThemeRepositoryPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(ThemeRepositoryInterface::class)) {
            return;
        }

        //  "on_invalid" keeps this a no-op on a theme-bundle without reset().
        $container
            ->getDefinition(ThemeRepositoryInterface::class)
            ->addTag('kernel.reset', ['method' => 'reset', 'on_invalid' => 'ignore'])
        ;
    }
}
