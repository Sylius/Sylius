<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\DependencyInjection\Compiler;

use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Factory\TranslatableFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ServicesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $factoryDefinition = new Definition(Factory::class);
        $factoryDefinition->addArgument(new Parameter('sylius.model.product.class'));

        $translatableFactoryDefinition = $container->getDefinition('sylius.factory.product');
        $productFactoryClass = $translatableFactoryDefinition->getClass();

        $translatableFactoryDefinition->setClass(TranslatableFactory::class);
        $translatableFactoryDefinition->setArguments([
            $factoryDefinition,
            new Reference('sylius.translation.locale_provider'),
        ]);

        $decoratedProductFactoryDefinition = new Definition($productFactoryClass);
        $decoratedProductFactoryDefinition->setArguments([
            $translatableFactoryDefinition,
            new Reference('sylius.repository.product_archetype'),
            new Reference('sylius.builder.product_archetype'),
            new Reference('sylius.factory.product_variant'),
        ]);

        $container->setDefinition('sylius.factory.product', $decoratedProductFactoryDefinition);

        $variantFactoryDefinition = $container->getDefinition('sylius.factory.product_variant');
        $variantFactoryClass = $variantFactoryDefinition->getClass();
        $variantFactoryDefinition->setClass(Factory::class);

        $decoratedProductVariantFactoryDefinition = new Definition($variantFactoryClass);
        $decoratedProductVariantFactoryDefinition
            ->addArgument($variantFactoryDefinition)
            ->addArgument(new Reference('sylius.repository.product'))
        ;

        $container->setDefinition('sylius.factory.product_variant', $decoratedProductVariantFactoryDefinition);
    }
}
