<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type\Checkout;

use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class ChangePaymentMethodType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
            $payments = $event->getData();
            $form = $event->getForm();

            foreach ($payments as $key => $payment) {
                if (!in_array($payment->getState(), [PaymentInterface::STATE_NEW, PaymentInterface::STATE_CART])) {
                    $form->remove($key);
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return CollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_change_payment_method';
    }
}
