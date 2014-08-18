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

use Symfony\Component\Form\FormBuilderInterface;

use Sylius\Bundle\OrderBundle\Form\Type\OrderType as BaseOrderType;

/**
 * Order form type.
 * We add two addresses to form, and that's all.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderType extends BaseOrderType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('shippingAddress', 'sylius_address')
            ->add('billingAddress', 'sylius_address')
        ;
    }
}
