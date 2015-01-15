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

use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class OrderFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.order_filter.number',
                'attr'     => array(
                    'placeholder' => 'sylius.form.order_filter.number'
                )
            ))
            ->add('totalFrom', 'sylius_money', array(
                'required' => false,
                'label'    => 'sylius.form.order_filter.total_from',
                'attr'     => array(
                    'placeholder' => 'sylius.form.order_filter.total_from'
                )
            ))
            ->add('totalTo', 'sylius_money', array(
                'required' => false,
                'label'    => 'sylius.form.order_filter.total_to',
                'attr'     => array(
                    'placeholder' => 'sylius.form.order_filter.total_to'
                )
            ))
            ->add('createdAtFrom', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.order_filter.created_at_from',
                'attr'     => array(
                    'placeholder' => 'sylius.form.order_filter.created_at_from'
                )
            ))
            ->add('createdAtTo', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.order_filter.created_at_to',
                'attr'     => array(
                    'placeholder' => 'sylius.form.order_filter.created_at_to'
                )
            ))
            ->add('paymentState', 'choice', array(
                'required'    => false,
                'label'       => 'sylius.form.order_filter.payment_state',
                'empty_value' => 'sylius.form.order_filter.payment_state',
                'choices'     => array(
                    PaymentInterface::STATE_NEW        => 'sylius.payment.state.new',
                    PaymentInterface::STATE_PENDING    => 'sylius.payment.state.pending',
                    PaymentInterface::STATE_PROCESSING => 'sylius.payment.state.processing',
                    PaymentInterface::STATE_COMPLETED  => 'sylius.payment.state.completed',
                    PaymentInterface::STATE_FAILED     => 'sylius.payment.state.failed',
                    PaymentInterface::STATE_CANCELLED  => 'sylius.payment.state.cancelled',
                    PaymentInterface::STATE_VOID       => 'sylius.payment.state.void',
                    PaymentInterface::STATE_REFUNDED   => 'sylius.payment.state.refunded',
                    PaymentInterface::STATE_UNKNOWN    => 'sylius.payment.state.unknown',
                ),
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_order_filter';
    }
}
