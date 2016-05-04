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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusShippingExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load(sprintf('driver/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);
        $this->mapFormValidationGroupsParameters($config, $container);

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $shippingMethod = $container->getDefinition('sylius.form.type.shipping_method');
        $shippingMethod->addArgument(new Reference('sylius.registry.shipping_calculator'));
        $shippingMethod->addArgument(new Reference('sylius.registry.shipping_rule_checker'));
        $shippingMethod->addArgument(new Reference('form.registry'));

        $shippingMethod = $container->getDefinition('sylius.form.type.shipping_method_rule');
        $shippingMethod->addArgument(new Reference('sylius.registry.shipping_rule_checker'));
    }
}
