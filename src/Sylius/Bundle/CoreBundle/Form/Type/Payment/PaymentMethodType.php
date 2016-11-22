<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Payment;

use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodType as BasePaymentMethodType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PaymentMethodType extends BasePaymentMethodType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('channels', ResourceChoiceType::class, [
                'resource' => 'sylius.channel',
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.payment_method.channels',
            ])
        ;
    }
}
