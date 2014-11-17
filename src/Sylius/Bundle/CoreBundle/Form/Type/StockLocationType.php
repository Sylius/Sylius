<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\InventoryBundle\Form\Type\StockLocationType as BaseStockLocationType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * StockLocation form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockLocationType extends BaseStockLocationType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('address', 'sylius_address')
        ;
    }
}
