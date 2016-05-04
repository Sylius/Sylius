<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PricingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Pricing extension
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusPricingExtension extends Extension
{
    protected $configFiles = [
        'services.xml',
        'templating.xml',
        'twig.xml',
    ];

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach ($config['forms'] as $formType) {
            $class = '%sylius.form.extension.priceable.class%';

            $definition = new Definition($class);
            $definition
                ->setArguments([
                    $formType,
                    new Reference('sylius.registry.price_calculator'),
                    new Reference('sylius.form.subscriber.priceable'),
                ])
                ->addTag('form.type_extension', ['alias' => $formType])
            ;

            $container->setDefinition(sprintf('sylius.form.extension.priceable.%s', $formType), $definition);
        }

        $loader->load('services.xml');
        $loader->load('templating.xml');
        $loader->load('twig.xml');
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'sylius_pricing';
    }
}
