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

namespace Sylius\Bundle\ShippingBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\CompositeShippingMethodEligibilityCheckerPass;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterCalculatorsPass;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterShippingMethodsResolversPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusShippingBundle extends AbstractResourceBundle
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

        $container->addCompilerPass(new CompositeShippingMethodEligibilityCheckerPass());
        $container->addCompilerPass(new RegisterCalculatorsPass());
        $container->addCompilerPass(new RegisterRuleCheckersPass());
        $container->addCompilerPass(new RegisterShippingMethodsResolversPass());
    }

    protected function getModelNamespace(): string
    {
        return 'Sylius\Component\Shipping\Model';
    }
}
