<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\DependencyInjection;

use Sylius\Bundle\TaxationBundle\SyliusTaxationBundle;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Taxation system extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusTaxationExtension extends Extension
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

        if (!in_array($driver, SyliusTaxationBundle::getSupportedDrivers())) {
            throw new \InvalidArgumentException(sprintf('Driver "%s" is unsupported for SyliusTaxationBundle', $driver));
        }

        $loader->load(sprintf('driver/%s.xml', $driver));

        $container->setParameter('sylius_taxation.driver', $driver);
        $container->setParameter('sylius_taxation.engine', $config['engine']);

        $classes = $config['classes'];

        $categoryClasses = $classes['category'];

        if (isset($categoryClasses['model'])) {
            $container->setParameter('sylius_taxation.model.category.class', $categoryClasses['model']);
        }

        if (isset($categoryClasses['repository'])) {
            $container->setParameter('sylius_taxation.repository.category.class', $categoryClasses['repository']);
        }

        $container->setParameter('sylius_taxation.controller.category.class', $categoryClasses['controller']);
        $container->setParameter('sylius_taxation.form.type.category.class', $categoryClasses['form']);

        $rateClasses = $classes['rate'];

        if (isset($rateClasses['model'])) {
            $container->setParameter('sylius_taxation.model.rate.class', $rateClasses['model']);
        }

        if (isset($rateClasses['repository'])) {
            $container->setParameter('sylius_taxation.repository.rate.class', $rateClasses['repository']);
        }

        $container->setParameter('sylius_taxation.controller.rate.class', $rateClasses['controller']);
        $container->setParameter('sylius_taxation.form.type.rate.class', $rateClasses['form']);

        $loader->load('services.xml');
    }
}
