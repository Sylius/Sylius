<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Payum extension.
 *
 * @author Maksim Kotlyar
 */
class SyliusPayumExtension extends AbstractResourceExtension implements PrependExtensionInterface
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

        $container->setParameter('payum.template.layout', $config['template']['layout']);
        $container->setParameter('payum.template.obtain_credit_card', $config['template']['obtain_credit_card']);
    }


    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('sylius_payment')) {
            return;
        }

        $config = $container->getExtensionConfig('payum');
        $gateways = [];
        foreach (array_keys($config[0]['gateways']) as $gatewayKey) {
            $gateways[$gatewayKey] = 'sylius.payum_gateway.'.$gatewayKey;
        }

        $container->prependExtensionConfig('sylius_payment', ['gateways' => $gateways]);
    }
}
