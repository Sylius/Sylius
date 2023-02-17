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

namespace Sylius\Bundle\OrderBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusOrderExtension extends AbstractResourceExtension
{
    public const CART_CONTEXT_TAG = 'sylius.context.cart';

    public const ORDER_PROCESSOR_TAG = 'sylius.order_processor';

    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load(sprintf('services/integrations/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $container->setParameter('sylius_order.cart_expiration_period', $config['expiration']['cart']);
        $container->setParameter('sylius_order.order_expiration_period', $config['expiration']['order']);

        $container
            ->registerForAutoconfiguration(CartContextInterface::class)
            ->addTag(self::CART_CONTEXT_TAG)
        ;
        $container
            ->registerForAutoconfiguration(OrderProcessorInterface::class)
            ->addTag(self::ORDER_PROCESSOR_TAG)
        ;
    }
}
