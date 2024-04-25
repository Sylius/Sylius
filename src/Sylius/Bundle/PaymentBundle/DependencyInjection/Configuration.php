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

namespace Sylius\Bundle\PaymentBundle\DependencyInjection;

use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodTranslationType;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodType;
use Sylius\Bundle\PaymentBundle\Form\Type\PaymentType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Payment\Model\Payment;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethod;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethodTranslation;
use Sylius\Component\Payment\Model\PaymentMethodTranslationInterface;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Factory\TranslatableFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_payment');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->arrayNode('gateways')
                    ->useAttributeAsKey('name')
                    ->scalarPrototype()
                ->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('payment_method')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/payment-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(PaymentMethod::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PaymentMethodInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->end()
                                        ->scalarNode('form')->defaultValue(PaymentMethodType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')
                                            ->setDeprecated('sylius/payment-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                        ->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(PaymentMethodTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(PaymentMethodTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                                ->scalarNode('form')->defaultValue(PaymentMethodTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('payment')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/payment-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Payment::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PaymentInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(PaymentType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
