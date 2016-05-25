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

use Sylius\Bundle\ProductBundle\Form\Type\VariantType;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Sylius\Component\Product\Factory\ProductVariantFactory;
use Sylius\Component\Product\Model\Attribute;
use Sylius\Component\Product\Model\AttributeInterface;
use Sylius\Component\Product\Model\AttributeTranslation;
use Sylius\Component\Product\Model\AttributeTranslationInterface;
use Sylius\Component\Product\Model\AttributeValue;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Product\Model\Option;
use Sylius\Component\Product\Model\OptionInterface;
use Sylius\Component\Product\Model\OptionTranslation;
use Sylius\Component\Product\Model\OptionTranslationInterface;
use Sylius\Component\Product\Model\OptionValue;
use Sylius\Component\Product\Model\OptionValueInterface;
use Sylius\Component\Product\Model\OptionValueTranslation;
use Sylius\Component\Product\Model\OptionValueTranslationInterface;
use Sylius\Component\Product\Model\Variant;
use Sylius\Component\Product\Model\VariantInterface;
use Sylius\Component\Resource\Factory;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * Product catalog extension.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusProductExtension extends AbstractResourceExtension implements PrependExtensionInterface
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

        $configFiles = [
            'services.xml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }

        $formDefinition = $container->getDefinition('sylius.form.type.product_variant_generation');
        $formDefinition->addArgument($container->getDefinition('sylius.form.listener.product_variant_generator'));
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $container->getExtensionConfig($this->getAlias()));

        $this->prependAttribute($container, $config);
        $this->prependVariation($container, $config);
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
                ], ]
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array $config
     */
    private function prependVariation(ContainerBuilder $container, array $config)
    {
        if (!$container->hasExtension('sylius_variation')) {
            return;
        }

        $container->prependExtensionConfig('sylius_variation', [
            'resources' => [
                'product' => [
                    'variable' => $config['resources']['product']['classes']['model'],
                    'variant' => [
                        'classes' => [
                            'model' => Variant::class,
                            'interface' => VariantInterface::class,
                            'factory' => ProductVariantFactory::class,
                            'form' => [
                                'default' => VariantType::class,
                            ],
                        ],
                    ],
                    'option' => [
                        'classes' => [
                            'model' => Option::class,
                            'interface' => OptionInterface::class,
                        ],
                        'translation' => [
                            'classes' => [
                                'model' => OptionTranslation::class,
                                'interface' => OptionTranslationInterface::class,
                            ],
                        ],
                    ],
                    'option_value' => [
                        'classes' => [
                            'model' => OptionValue::class,
                            'interface' => OptionValueInterface::class,
                        ],
                        'translation' => [
                            'classes' => [
                                'model' => OptionValueTranslation::class,
                                'interface' => OptionValueTranslationInterface::class,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
