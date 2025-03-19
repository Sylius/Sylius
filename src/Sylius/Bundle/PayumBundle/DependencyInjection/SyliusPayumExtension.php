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

namespace Sylius\Bundle\PayumBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusPayumExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $bundles = $container->getParameter('kernel.bundles');
        if (array_key_exists('SyliusShopBundle', $bundles)) {
            $loader->load('services/integrations/sylius_shop.xml');
        }

        $container->setParameter('payum.template.layout', $config['template']['layout']);
        $container->setParameter('payum.template.obtain_credit_card', $config['template']['obtain_credit_card']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependSyliusPayment($container);
    }

    private function prependSyliusPayment(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('sylius_payment')) {
            return;
        }

        $gateways = [];
        $gatewayFactories = [];
        $configs = $container->getExtensionConfig('payum');
        foreach ($configs as $config) {
            if (!isset($config['gateways'])) {
                continue;
            }
            foreach ($config['gateways'] as $gatewayKey => $gatewayConfig) {
                $gateways[$gatewayKey] = 'sylius.payum_gateway.' . $gatewayKey;
                $gatewayFactories[] = $gatewayConfig['factory'] ?? null;
            }
        }

        $container->prependExtensionConfig('sylius_payment', ['gateways' => $gateways]);
        $container->prependExtensionConfig('sylius_payment', ['encryption' => [
            'disabled_for_factories' => array_filter($gatewayFactories),
        ]]);
    }
}
