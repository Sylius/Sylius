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

namespace Sylius\Bundle\OrderBundle\DependencyInjection;

use Sylius\Bundle\OrderBundle\Attribute\AsCartContext;
use Sylius\Bundle\OrderBundle\Attribute\AsOrderProcessor;
use Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterCartContextsPass;
use Sylius\Bundle\OrderBundle\DependencyInjection\Compiler\RegisterProcessorsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
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

        $this->registerAutoconfiguration($container, $config['autoconfigure_with_attributes']);
    }

    private function registerAutoconfiguration(ContainerBuilder $container, bool $autoconfigureWithAttributes): void
    {
        if (true === $autoconfigureWithAttributes) {
            $container->registerAttributeForAutoconfiguration(
                AsCartContext::class,
                static function (ChildDefinition $definition, AsCartContext $attribute): void {
                    $definition->addTag(AsCartContext::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
                },
            );
            $container->registerAttributeForAutoconfiguration(
                AsOrderProcessor::class,
                static function (ChildDefinition $definition, AsOrderProcessor $attribute): void {
                    $definition->addTag(AsOrderProcessor::SERVICE_TAG, ['priority' => $attribute->getPriority()]);
                },
            );
        } else {
            $container
                ->registerForAutoconfiguration(CartContextInterface::class)
                ->addTag(RegisterCartContextsPass::CART_CONTEXT_SERVICE_TAG)
            ;
            $container
                ->registerForAutoconfiguration(OrderProcessorInterface::class)
                ->addTag(RegisterProcessorsPass::PROCESSOR_SERVICE_TAG)
            ;
        }
    }
}
