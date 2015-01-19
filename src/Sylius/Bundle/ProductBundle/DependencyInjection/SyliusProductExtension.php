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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

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
                            'model'       => 'Sylius\Component\Product\Model\Attribute',
                            'interface'   => 'Sylius\Component\Product\Model\AttributeInterface',
                            'repository'  => 'Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository',
                        ),
                        'translation' => array(
                            'classes' => array(
                                'model' => 'Sylius\Component\Product\Model\AttributeTranslation'
                            )
                        )
                    ),
                    'attribute_value' => array(
                        'classes' => array(
                            'model'     => 'Sylius\Component\Product\Model\AttributeValue',
                            'interface' => 'Sylius\Component\Product\Model\AttributeValueInterface'
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
                            'model'      => 'Sylius\Component\Product\Model\Variant',
                            'interface'  => 'Sylius\Component\Product\Model\VariantInterface',
                            'controller' => 'Sylius\Bundle\ProductBundle\Controller\VariantController',
                            'form' => array(
                                'default' => 'Sylius\Bundle\ProductBundle\Form\Type\VariantType'
                            )
                        )
                    ),
                    'option'       => array(
                        'classes' => array(
                            'model'       => 'Sylius\Component\Product\Model\Option',
                            'interface'   => 'Sylius\Component\Product\Model\OptionInterface',
                            'repository'  => 'Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository',
                        ),
                        'translation' => array(
                            'classes' => array(
                                'model' => 'Sylius\Component\Product\Model\OptionTranslation'
                            )
                        )
                    ),
                    'option_value' => array(
                        'classes' => array(
                            'model'     => 'Sylius\Component\Product\Model\OptionValue',
                            'interface' => 'Sylius\Component\Product\Model\OptionValueInterface'
                        )
                    ),
                )
            )
        ));
    }
}
