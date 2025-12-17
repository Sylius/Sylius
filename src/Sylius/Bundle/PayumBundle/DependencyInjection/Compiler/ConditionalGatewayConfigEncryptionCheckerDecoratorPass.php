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

namespace Sylius\Bundle\PayumBundle\DependencyInjection\Compiler;

use Sylius\Bundle\PayumBundle\Checker\GatewayConfigEncryptionChecker;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @internal
 *
 * @experimental
 */
final class ConditionalGatewayConfigEncryptionCheckerDecoratorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sylius.checker.gateway_config_encryption')) {
            return;
        }

        $container->register('sylius_payum.checker.gateway_config_encryption', GatewayConfigEncryptionChecker::class)
            ->setDecoratedService('sylius.checker.gateway_config_encryption')
            ->addArgument(new Reference('.inner'));
    }
}
