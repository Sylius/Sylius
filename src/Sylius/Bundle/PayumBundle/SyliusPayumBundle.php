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

namespace Sylius\Bundle\PayumBundle;

use Sylius\Bundle\PayumBundle\DependencyInjection\Compiler\InjectContainerIntoControllersPass;
use Sylius\Bundle\PayumBundle\DependencyInjection\Compiler\RegisterGatewayConfigTypePass;
use Sylius\Bundle\PayumBundle\DependencyInjection\Compiler\UnregisterPaypalGatewayTypePass;
use Sylius\Bundle\PayumBundle\DependencyInjection\Compiler\UnregisterStripeGatewayTypePass;
use Sylius\Bundle\PayumBundle\DependencyInjection\Compiler\UseTweakedDoctrineStoragePass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusPayumBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new InjectContainerIntoControllersPass());
        $container->addCompilerPass(new RegisterGatewayConfigTypePass());
        $container->addCompilerPass(new UseTweakedDoctrineStoragePass());
        $container->addCompilerPass(new UnregisterPaypalGatewayTypePass(), priority: 128);
        $container->addCompilerPass(new UnregisterStripeGatewayTypePass(), priority: 128);
    }
}
