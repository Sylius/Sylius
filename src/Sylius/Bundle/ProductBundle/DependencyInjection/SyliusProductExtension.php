<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Product\Model\Attribute;
use Sylius\Component\Product\Model\AttributeInterface;
use Sylius\Component\Product\Model\AttributeTranslation;
use Sylius\Component\Product\Model\AttributeTranslationInterface;
use Sylius\Component\Product\Model\AttributeValue;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class SyliusProductExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration($config, $container), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load(sprintf('driver/%s.xml', $config['driver']));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $loader->load('services.xml');

        $formDefinition = $container->getDefinition('sylius.form.type.product_variant_generation');
        $formDefinition->addArgument($container->getDefinition('sylius.form.listener.product_variant_generator'));

        $container->getDefinition('sylius.form.type.product')->addArgument(new Reference('sylius.variant_resolver.default'));
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        $this->prependAttribute($container, $config);
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    private function prependAttribute(ContainerBuilder $container, array $config)
    {
        if (!$container->hasExtension('sylius_attribute')) {
            return;
        }

        $container->prependExtensionConfig('sylius_attribute', [
            'resources' => [
                'product' => [
                    'subject' => $config['resources']['product']['classes']['model'],
                    'attribute' => [
                        'classes' => [
                            'model' => Attribute::class,
                            'interface' => AttributeInterface::class,
                        ],
                        'translation' => [
                            'classes' => [
                                'model' => AttributeTranslation::class,
                                'interface' => AttributeTranslationInterface::class,
                            ],
                        ],
                    ],
                    'attribute_value' => [
                        'classes' => [
                            'model' => AttributeValue::class,
                            'interface' => AttributeValueInterface::class,
                        ],
                    ],
                ],
            ],
        ]);
    }
}
