<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Checkout;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AddressingStepType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!array_key_exists('differentBillingAddress', $data) || false === $data['differentBillingAddress']) {
                    $data['billingAddress'] = $data['shippingAddress'];

                    $event->setData($data);
                }
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                /* @var CustomerInterface $customer */
                $customer = $options['customer'];
                // if customer does not have user, it is not registered, so we do not preset data
                if (null === $customer || !$customer instanceof CustomerInterface || !$customer->hasUser()) {
                    return;
                }

                /* @var $order OrderInterface */
                $order = $event->getData();
                if ($order->getShippingAddress() === null && $customer->getShippingAddress() !== null) {
                    $address = clone $customer->getShippingAddress();
                    $address->setCustomer(null);
                    $order->setShippingAddress($address);
                }

                if ($order->getBillingAddress() === null && $customer->getBillingAddress() !== null) {
                    $address = clone $customer->getBillingAddress();
                    $address->setCustomer(null);
                    $order->setBillingAddress($address);
                }
            })
            ->add('shippingAddress', 'sylius_address', ['shippable' => true])
            ->add('billingAddress', 'sylius_address')
            ->add('differentBillingAddress', 'checkbox', [
                'mapped' => false,
                'required' => false,
                'label' => 'sylius.form.checkout.addressing.different_billing_address',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'customer' => null,
                'cascade_validation' => true,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_checkout_addressing';
    }
}
