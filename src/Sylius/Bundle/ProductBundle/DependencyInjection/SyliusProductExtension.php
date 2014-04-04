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

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * Sylius product catalog system container extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusProductExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS);
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

    private function prependAttribute(ContainerBuilder $container, array $config)
    {
        if (!$container->hasExtension('sylius_attribute')) {
            return;
        }

        $container->prependExtensionConfig('sylius_attribute', array(
            'classes' => array(
                'product' => array(
                    'subject'   => $config['classes']['product']['model'],
                    'attribute' => array(
                        'model' => 'Sylius\Component\Product\Model\Attribute'
                    ),
                    'attribute_value' => array(
                        'model' => 'Sylius\Component\Product\Model\AttributeValue'
                    ),
                )
            ))
        );
    }

    private function prependVariation(ContainerBuilder $container, array $config)
    {
        if (!$container->hasExtension('sylius_variation')) {
            return;
        }

        $container->prependExtensionConfig('sylius_variation', array(
            'classes' => array(
                'product' => array(
                    'variable'   => $config['classes']['product']['model'],
                    'variant' => array(
                        'model'      => 'Sylius\Component\Product\Model\Variant',
                        'controller' => 'Sylius\Bundle\ProductBundle\Controller\VariantController',
                        'form'       => 'Sylius\Bundle\ProductBundle\Form\Type\VariantType'
                    ),
                    'option' => array(
                        'model' => 'Sylius\Component\Product\Model\Option'
                    ),
                    'option_value' => array(
                        'model' => 'Sylius\Component\Product\Model\OptionValue'
                    ),
                )
            ))
        );
    }
}
