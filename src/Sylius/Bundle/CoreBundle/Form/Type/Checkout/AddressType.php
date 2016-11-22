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

use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddCustomerGuestTypeFormSubscriber;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddDefaultBillingAddressOnOrderFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class AddressType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingAddress', 'sylius_address', [
                'shippable' => true,
                'constraints' => [new Valid()],
            ])
            ->add('billingAddress', 'sylius_address', [
                'constraints' => [new Valid()],
            ])
            ->add('differentBillingAddress', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'sylius.form.checkout.addressing.different_billing_address',
            ])
            ->addEventSubscriber(new AddDefaultBillingAddressOnOrderFormSubscriber())
            ->addEventSubscriber(new AddCustomerGuestTypeFormSubscriber('customer'))
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
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_checkout_address';
    }
}
