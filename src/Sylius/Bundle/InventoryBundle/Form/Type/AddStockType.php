<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Add stock to a product on a location
 *
 * @author Patrick Berenschot <p.berenschot@take-a-byte.eu>
 */
class AddStockType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', 'integer')
            ->add('stockLocation', 'entity', array('class' => 'Sylius\Component\Inventory\Model\StockLocation', 'property' => 'name'))
            ->add('productVariant', 'entity', array('class' => 'Sylius\Component\Core\Model\ProductVariant', 'choices' => $options['variants']))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_stock';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array('variants' => array()));
    }
}
