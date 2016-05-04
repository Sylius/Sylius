<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusPaymentExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $container->setParameter('sylius.payment_gateways', $config['gateways']);

        $factoryDefinition = new Definition(Factory::class, [new Parameter('sylius.model.payment.class')]);
        $paymentFactoryClass = $container->getParameter('sylius.factory.payment.class');
        $decoratedPaymentFactoryDefinition = new Definition($paymentFactoryClass, [$factoryDefinition]);

        $container->setDefinition('sylius.factory.payment', $decoratedPaymentFactoryDefinition);
    }
}
