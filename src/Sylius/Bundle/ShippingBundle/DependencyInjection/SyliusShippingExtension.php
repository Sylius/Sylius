<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Shipping extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusShippingExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configure(
            $config,
            new Configuration(),
            $container,
            self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS | self::CONFIGURE_TRANSLATIONS | self::CONFIGURE_FORMS
        );

        $shippingMethod = $container->getDefinition('sylius.form.type.shipping_method');
        $shippingMethod->addArgument(new Reference('sylius.shipping_calculator_registry'));
        $shippingMethod->addArgument(new Reference('sylius.shipping_rule_checker_registry'));

        $shippingMethod = $container->getDefinition('sylius.form.type.shipping_method_rule');
        $shippingMethod->addArgument(new Reference('sylius.shipping_rule_checker_registry'));
    }
}
