<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Payments dependency injection extension.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class SyliusPaymentsExtension extends Extension
{
    /**
     * Loads the extension
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $container->setDefinition('sylius.payments.gateway_factory', new Definition(
            'Tala\\GatewayFactory'
        ));

        foreach ($config['gateways'] as $name => $options) {
            $this->createGatewayService($container, $name, $options);
        }
    }

    public function createGatewayService(ContainerBuilder $container, $name, array $options)
    {
        $type = str_replace('_', '\\', $options['type']);
        if (false === strpos($type, '\\')) {
            $type .= '\\';
        }

        $container
            ->setDefinition(sprintf('sylius.payments.gateway_%s', $name), new Definition(
                'Tala\\Billing\\'.$type.'Gateway'
            ))
            ->setFactoryClass(
                'sylius.payments.gateway_factory'
            )->setFactoryMethod(
                'createGateway'
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'sylius_payments';
    }
}
