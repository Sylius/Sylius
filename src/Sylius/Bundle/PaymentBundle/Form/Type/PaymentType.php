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

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PaymentType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('method', PaymentMethodChoiceType::class, [
                'label' => 'sylius.form.payment.method',
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'sylius.form.payment.amount',
            ])
            ->add('state', ChoiceType::class, [
                'choices' => [
                    'sylius.form.payment.state.processing' => PaymentInterface::STATE_PROCESSING,
                    'sylius.form.payment.state.failed' => PaymentInterface::STATE_FAILED,
                    'sylius.form.payment.state.completed' => PaymentInterface::STATE_COMPLETED,
                    'sylius.form.payment.state.new' => PaymentInterface::STATE_NEW,
                    'sylius.form.payment.state.cancelled' => PaymentInterface::STATE_CANCELLED,
                    'sylius.form.payment.state.refunded' => PaymentInterface::STATE_REFUNDED,
                ],
                'label' => 'sylius.form.payment.state.header',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_payment';
    }
}
