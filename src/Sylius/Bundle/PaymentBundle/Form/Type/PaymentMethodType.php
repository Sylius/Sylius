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

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Payment method form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PaymentMethodType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'sylius_translations', [
                'type' => 'sylius_payment_method_translation',
                'label' => 'sylius.form.payment_method.name',
            ])
            ->add('gateway', 'sylius_payment_gateway_choice', [
                'label' => 'sylius.form.payment_method.gateway',
            ])
            ->add('enabled', 'checkbox', [
                'required' => false,
                'label' => 'sylius.form.payment_method.enabled',
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_payment_method';
    }
}
