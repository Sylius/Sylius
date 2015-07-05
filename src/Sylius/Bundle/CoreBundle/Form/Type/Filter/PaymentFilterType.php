<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Payment filter type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PaymentFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.payment_filter.number',
                'attr'     => array(
                    'placeholder' => 'sylius.form.payment_filter.number'
                )
            ))
            ->add('billingAddress', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.payment_filter.billing_address',
                'attr'     => array(
                    'placeholder' => 'sylius.form.payment_filter.billing_address'
                )
            ))
            ->add('createdAtFrom', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.payment_filter.created_at_from',
                'attr'     => array(
                    'placeholder' => 'sylius.form.payment_filter.created_at_from'
                )
            ))
            ->add('createdAtTo', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.payment_filter.created_at_to',
                'attr'     => array(
                    'placeholder' => 'sylius.form.payment_filter.created_at_to'
                )
            ))
            ->add('channel', 'sylius_channel_choice', array(
                'required'    => false,
                'empty_value' => 'sylius.form.payment_filter.channel'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_payment_filter';
    }
}
