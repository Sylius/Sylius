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

/**
 * Inventory Stock type.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class StockType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('manage_stock', 'checkbox', array(
                'label' => 'sylius.form.stock.manage_stock'
            ))
            ->add('onHand', null, array(
                'label' => 'sylius.form.stock.onHand'
            ))
            ->add('allowBackorders', 'checkbox', array(
                'label' => 'sylius.form.stock.allowBackorders'
            ))
            ->add('minQuantityInCart', 'number', array(
                'label' => 'sylius.form.stock.minQuantityInCart'
            ))
            ->add('maxQuantityInCart', 'number', array(
                'label' => 'sylius.form.stock.maxQuantityInCart'
            ))
            ->add('minStockLevel', 'number', array(
                'label' => 'sylius.form.stock.minStockLevel'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_inventory_stock';
    }
}
