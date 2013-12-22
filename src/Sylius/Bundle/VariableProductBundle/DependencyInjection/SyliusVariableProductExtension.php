<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\BaseExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * Sylius product catalog system container extension.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusVariableProductExtension extends BaseExtension implements PrependExtensionInterface
{
    protected $configFiles = array(
        'options',
        'variants',
        'prototypes',
    );

    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $this->configDir = __DIR__.'/../Resources/config/container';

        $config[0]['driver'] = $container->getParameter('sylius_product.driver');
        $this->configure($config, new Configuration(), $container, self::CONFIGURE_LOADER | self::CONFIGURE_DATABASE | self::CONFIGURE_PARAMETERS | self::CONFIGURE_VALIDATORS);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if (!$container->hasExtension('sylius_product')) {
            return;
        }

        $container->prependExtensionConfig('sylius_product', array(
            'classes' => array(
                'product' => array(
                    'model' => 'Sylius\Bundle\VariableProductBundle\Model\VariableProduct',
                    'form'  => 'Sylius\Bundle\VariableProductBundle\Form\Type\VariableProductType'
                ),
                'prototype' => array(
                    'model' => 'Sylius\Bundle\VariableProductBundle\Model\Prototype',
                    'form'  => 'Sylius\Bundle\VariableProductBundle\Form\Type\PrototypeType'
                )
            ))
        );
    }
}
