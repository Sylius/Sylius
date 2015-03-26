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
use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Checkout addressing step form type.
 *
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
            ->add('shippingAddress', 'sylius_address', array('shippable' => true))
            ->add('billingAddress', 'sylius_address')
            ->add('differentBillingAddress', 'checkbox', array(
                'mapped'   => false,
                'required' => false,
                'label'    => 'sylius.form.checkout.addressing.different_billing_address',
            ))
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $data = $event->getData();

                if (!array_key_exists('differentBillingAddress', $data) || false === $data['differentBillingAddress']) {
                    $data['billingAddress'] = $data['shippingAddress'];

                    $event->setData($data);
                }
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                /* @var $user UserInterface */
                $user = $options['user'];
                /* @var $order OrderInterface */
                $order = $event->getData();
                if (null === $user || !$user instanceof UserInterface) {
                    if ($user instanceof CustomerInterface) {
                        if (null !== $order->getShippingAddress()) {
                            $user->addAddress($order->getShippingAddress());
                        }

                        if (null !== $order->getBillingAddress()) {
                            $user->addAddress($order->getBillingAddress());
                        }
                    }

                    return;
                }

                /** @var $order OrderInterface */
                $order = $event->getData();
                if (null === $order->getShippingAddress() && null !== $user->getShippingAddress()) {
                    $address = clone $user->getShippingAddress();
                    $address->setCustomer(null);
                    $order->setShippingAddress($address);
                }

                if (null === $order->getBillingAddress() && null !== $user->getBillingAddress()) {
                    $address = clone $user->getBillingAddress();
                    $address->setCustomer(null);
                    $order->setBillingAddress($address);
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setDefaults(array(
                'user' => null,
                'cascade_validation' => true,
            ))
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
