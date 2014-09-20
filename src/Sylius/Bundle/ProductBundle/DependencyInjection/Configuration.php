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

use Sylius\Bundle\ResourceBundle\DependencyInjection\AbstractResourceConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class Configuration extends AbstractResourceConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_product');

        $this
            ->addDefaults($rootNode, null, null, array(
                'product' => array('sylius'),
                'product_prototype' => array('sylius'),
                'product_translation' => array('sylius'),
            ))
        ;

        $rootNode
            ->append($this->createResourcesSection(array(
                    'product'           => array(
                        'model' => 'Sylius\Component\Product\Model\Product',
                        'form'  => 'Sylius\Bundle\ProductBundle\Form\Type\ProductType',
                    ),
                    'product_translation'           => array(
                        'model' => 'Sylius\Component\Core\Model\ProductTranslation',
                    ),
                    'product_prototype' => array(
                        'model'      => 'Sylius\Component\Product\Model\Prototype',
                        'controller' => 'Sylius\Bundle\ProductBundle\Controller\PrototypeController',
                        'form'       => 'Sylius\Bundle\ProductBundle\Form\Type\PrototypeType',
                    ),
                ))
            )
        ;

        return $treeBuilder;
    }
}
