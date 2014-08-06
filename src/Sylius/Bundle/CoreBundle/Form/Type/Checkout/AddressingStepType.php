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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Checkout addressing step form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AddressingStepType extends AbstractType
{
    protected $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

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
                /* @var $user UserInterface */
                $user = $options['user'];
                if (null === $user || !$user instanceof UserInterface) {
                    return;
                }
                /* @var $order OrderInterface */
                $order = $event->getData();
                if ($order->getShippingAddress() === null && $user->getShippingAddress() !== null) {
                    $order->setShippingAddress(clone $user->getShippingAddress());
                }
                if ($order->getBillingAddress() === null && $user->getBillingAddress() !== null) {
                    $order->setBillingAddress(clone $user->getBillingAddress());
                }
            })
            ->add('shippingAddress', 'sylius_address', array('shippable' => true))
            ->add('billingAddress', 'sylius_address')
            ->add('differentBillingAddress', 'checkbox', array(
                'mapped' => false,
                'label'  => 'sylius.form.checkout.addressing.different_billing_address'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass,
                'cascade_validation' => true,
                'user' => null
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
