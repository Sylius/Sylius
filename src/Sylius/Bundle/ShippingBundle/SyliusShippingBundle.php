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

namespace Sylius\Bundle\ShippingBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterCalculatorsPass;
use Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler\RegisterShippingMethodsResolversPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusShippingBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterCalculatorsPass());
        $container->addCompilerPass(new RegisterShippingMethodsResolversPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace(): string
    {
        return 'Sylius\Component\Shipping\Model';
    }
}
