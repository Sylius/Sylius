<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\DependencyInjection;

use Sylius\Bundle\TaxonomiesBundle\SyliusTaxonomiesBundle;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Categorization bundle extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusTaxonomiesExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $driver = $config['driver'];
        $engine = $config['engine'];

        if (!in_array($driver, SyliusTaxonomiesBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported by SyliusTaxonomiesBundle.', $config['driver']));
        }

        $loader->load(sprintf('driver/%s.xml', $driver));

        $container->setParameter('sylius_taxonomies.driver', $driver);
        $container->setParameter('sylius_taxonomies.engine', $engine);

        $classes = $config['classes'];

        $taxonomyClasses = $classes['taxonomy'];
        $taxonClasses = $classes['taxon'];

        $loader->load('services.xml');

        $container->setParameter('sylius_taxonomies.model.taxonomy.class', $taxonomyClasses['model']);
        $container->setParameter('sylius_taxonomies.controller.taxonomy.class', $taxonomyClasses['controller']);
        $container->setParameter('sylius_taxonomies.form.type.taxonomy.class', $taxonomyClasses['form']);

        if (isset($taxonomyClasses['repository'])) {
            $container->setParameter('sylius_taxonomies.repository.taxonomy.class', $taxonomyClasses['repository']);
        }

        $container->setParameter('sylius_taxonomies.model.taxon.class', $taxonClasses['model']);
        $container->setParameter('sylius_taxonomies.controller.taxon.class', $taxonClasses['controller']);
        $container->setParameter('sylius_taxonomies.form.type.taxon.class', $taxonClasses['form']);

        if (isset($taxonClasses['repository'])) {
            $container->setParameter('sylius_taxonomies.repository.taxon.class', $taxonClasses['repository']);
        }
    }
}
