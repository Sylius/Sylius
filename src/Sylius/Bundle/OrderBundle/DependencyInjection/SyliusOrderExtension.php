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

use Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterCartContextsPass;
use Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterProcessorsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Order\Attribute\AsCartContext;
use Sylius\Component\Order\Attribute\AsOrderProcessor;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusOrderExtension extends AbstractResourceExtension
{
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
            ->addTag(RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG)
        ;

        $container->registerAttributeForAutoconfiguration(
            AsCartContext::class,
            static function (ChildDefinition $definition, AsCartContext $attribute) {
                $definition->addTag(RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG, [
                    'priority' => $attribute->priority,
                ]);
            }
        );

        $container
            ->registerForAutoconfiguration(OrderProcessorInterface::class)
            ->addTag(RegisterProcessorsPass::PROCESSOR_SERVICE_TAG)
        ;

        $container->registerAttributeForAutoconfiguration(
            AsOrderProcessor::class,
            static function (ChildDefinition $definition, AsOrderProcessor $attribute) {
                $definition->addTag(RegisterProcessorsPass::PROCESSOR_SERVICE_TAG, [
                    'priority' => $attribute->priority,
                ]
            );
        });

        $container->registerAttributeForAutoconfiguration(
            AsOrderProcessor::class,
            static function (ChildDefinition $definition, AsOrderProcessor $attribute) {
                $definition->addTag(RegisterProcessorsPass::PROCESSOR_SERVICE_TAG, [
                    'priority' => $attribute->priority,
                ]);
            }
        );
    }
}
