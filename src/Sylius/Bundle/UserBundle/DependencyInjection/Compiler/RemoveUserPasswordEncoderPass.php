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

namespace Sylius\Bundle\UserBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

final class RemoveUserPasswordEncoderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (interface_exists(EncoderFactoryInterface::class)) {
            return;
        }

        $container->removeDefinition('sylius.security.password_encoder');
    }
}
