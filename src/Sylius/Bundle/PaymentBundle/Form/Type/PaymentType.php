<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Payment form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PaymentType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('method', 'sylius_payment_method_choice', [
                'label' => 'sylius.form.payment.method',
            ])
            ->add('amount', 'sylius_money', [
                'label' => 'sylius.form.payment.amount',
            ])
            ->add('state', 'choice', [
                'label' => 'sylius.form.payment.state.header',
                'choices' => [
                    PaymentInterface::STATE_PROCESSING => 'sylius.form.payment.state.processing',
                    PaymentInterface::STATE_PENDING => 'sylius.form.payment.state.pending',
                    PaymentInterface::STATE_FAILED => 'sylius.form.payment.state.failed',
                    PaymentInterface::STATE_VOID => 'sylius.form.payment.state.void',
                    PaymentInterface::STATE_COMPLETED => 'sylius.form.payment.state.completed',
                    PaymentInterface::STATE_AUTHORIZED => 'sylius.form.payment.state.authorized',
                    PaymentInterface::STATE_NEW => 'sylius.form.payment.state.new',
                    PaymentInterface::STATE_CANCELLED => 'sylius.form.payment.state.cancelled',
                    PaymentInterface::STATE_REFUNDED => 'sylius.form.payment.state.refunded',
                    PaymentInterface::STATE_UNKNOWN => 'sylius.form.payment.state.unknown',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_payment';
    }
}
