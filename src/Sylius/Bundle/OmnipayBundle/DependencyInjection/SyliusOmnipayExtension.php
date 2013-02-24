<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OmnipayBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Payments dependency injection extension.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class SyliusOmnipayExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), $configs);

        $container->setDefinition('sylius.omnipay.gateway_factory', new Definition(
            'Omnipay\\Common\\GatewayFactory'
        ));

        foreach ($config['gateways'] as $name => $parameters) {
            $this->createGatewayService($container, $name, $parameters);
        }
    }

    /**
     * Create gateway service.
     *
     * @param ContainerBuilder $container
     * @param string           $name
     * @param array            $parameters
     */
    public function createGatewayService(ContainerBuilder $container, $name, array $parameters)
    {
        $type = str_replace('_', '\\', $parameters['type']);
        unset($parameters['type']);

        $class = 'Omnipay\\'.$type.'\\Gateway';

        $definition = new Definition($class);
        $definition
            ->setFactoryService('sylius.omnipay.gateway_factory')
            ->setFactoryMethod('create')
            ->setArguments(array($type))
        ;

        $reflection = new \ReflectionClass($class);
        foreach ($parameters['options'] as $optionName => $value) {
            $method = 'set'.ucfirst($optionName);

            if ($reflection->hasMethod($method)) {
                $definition->addMethodCall($method, array($value));
            }
        }

        $container->setDefinition(sprintf('sylius.omnipay.gateway.%s', $name), $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'sylius_omnipay';
    }
}
