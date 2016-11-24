<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodChoiceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @internal
 *
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class AddPaymentMethodsFormSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $payment = $event->getData();

        $form->add('method', PaymentMethodChoiceType::class, [
            'label' => 'sylius.form.checkout.payment_method',
            'subject' => $payment,
            'expanded' => true,
        ]);
    }
}
