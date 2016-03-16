<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomyBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Translation\Factory\TranslatableFactory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusTaxonomyExtension extends AbstractResourceExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load(sprintf('driver/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $factoryDefinition = new Definition(Factory::class);
        $factoryDefinition->setArguments(
            [
                new Parameter('sylius.model.taxonomy.class'),
            ]
        );

        $translatableFactoryDefinition = $container->getDefinition('sylius.factory.taxonomy');
        $taxonomyFactoryClass = $translatableFactoryDefinition->getClass();
        $translatableFactoryDefinition->setClass(TranslatableFactory::class);
        $translatableFactoryDefinition->setArguments(
            [
                $factoryDefinition,
                new Reference('sylius.translation.locale_provider'),
            ]
        );

        $decoratedTaxonomyFactoryDefinition = new Definition($taxonomyFactoryClass);
        $decoratedTaxonomyFactoryDefinition->setArguments(
            [
                $translatableFactoryDefinition,
                new Reference('sylius.factory.taxon'),
            ]
        );

        $container->setDefinition('sylius.factory.taxonomy', $decoratedTaxonomyFactoryDefinition);

        $taxonFactoryDefinition = $container->getDefinition('sylius.factory.taxon');
        $taxonFactoryClass = $taxonFactoryDefinition->getClass();
        $taxonFactoryDefinition->setClass(TranslatableFactory::class);

        $decoratedTaxonFactoryDefinition = new Definition($taxonFactoryClass);
        $decoratedTaxonFactoryDefinition
            ->addArgument($taxonFactoryDefinition)
            ->addArgument(new Reference('sylius.repository.taxonomy'))
        ;
        $container->setDefinition('sylius.factory.taxon', $decoratedTaxonFactoryDefinition);
    }
}
