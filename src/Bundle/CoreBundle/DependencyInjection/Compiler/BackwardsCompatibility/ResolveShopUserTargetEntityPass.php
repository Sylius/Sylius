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

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\BackwardsCompatibility;

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @internal
 */
final class ResolveShopUserTargetEntityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        try {
            $resolveTargetEntityListener = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');
            $shopUserClass = $container->getParameter('sylius.model.shop_user.class');
        } catch (InvalidArgumentException) {
            return;
        }

        $resolveTargetEntityListener->addMethodCall(
            'addResolveTargetEntity',
            [UserInterface::class, $shopUserClass, []],
        );
    }
}
