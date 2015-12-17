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

use Sylius\Bundle\ProductBundle\Controller\VariantController;
use Sylius\Bundle\ProductBundle\Form\Type\VariantType;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
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
use Sylius\Component\Product\Model\Variant;
use Sylius\Component\Product\Model\VariantInterface;
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
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->registerResources('sylius', $config['driver'], $config['resources'], $container);

        $configFiles = array(
            'services.xml',
        );

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }
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
     * @param array            $config
     */
    private function prependAttribute(ContainerBuilder $container, array $config)
    {
        if (!$container->hasExtension('sylius_attribute')) {
            return;
        }

        $container->prependExtensionConfig('sylius_attribute', array(
                'resources' => array(
                    'product' => array(
                        'subject'         => $config['resources']['product']['classes']['model'],
                        'attribute'       => array(
                            'classes' => array(
                                'model'       => Attribute::class,
                                'interface'   => AttributeInterface::class,
                            ),
                            'translation' => array(
                                'classes' => array(
                                    'model' => AttributeTranslation::class,
                                    'interface' => AttributeTranslationInterface::class,
                                )
                            )
                        ),
                        'attribute_value' => array(
                            'classes' => array(
                                'model'     => AttributeValue::class,
                                'interface' => AttributeValueInterface::class,
                            )
                        ),
                    ),
                ))
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function prependVariation(ContainerBuilder $container, array $config)
    {
        if (!$container->hasExtension('sylius_variation')) {
            return;
        }

        $container->prependExtensionConfig('sylius_variation', array(
            'resources' => array(
                'product' => array(
                    'variable' => $config['resources']['product']['classes']['model'],
                    'variant'  => array(
                        'classes' => array(
                            'model'      => Variant::class,
                            'interface'  => VariantInterface::class,
                            'controller' => VariantController::class,
                            'form' => array(
                                'default' => VariantType::class
                            )
                        )
                    ),
                    'option'       => array(
                        'classes' => array(
                            'model'       => Option::class,
                            'interface'   => OptionInterface::class,
                        ),
                        'translation' => array(
                            'classes' => array(
                                'model' => OptionTranslation::class,
                                'interface' => OptionTranslationInterface::class
                            )
                        )
                    ),
                    'option_value' => array(
                        'classes' => array(
                            'model'     => OptionValue::class,
                            'interface' => OptionValueInterface::class,
                        )
                    ),
                )
            )
        ));
    }
}
