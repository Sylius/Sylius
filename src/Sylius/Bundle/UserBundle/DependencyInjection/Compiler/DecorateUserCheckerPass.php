<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\DependencyInjection\Compiler;

use Sylius\Component\User\Security\Checker\UserChecker;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class DecorateUserCheckerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('security.user_checker')) {
            return;
        }

        $definition = new Definition(UserChecker::class);
        $definition->setDecoratedService('security.user_checker');
        $definition->addArgument(new Reference(UserChecker::class . '.inner'));

        $container->setDefinition(UserChecker::class, $definition);
    }
}
