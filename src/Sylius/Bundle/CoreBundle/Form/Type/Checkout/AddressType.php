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

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType as SyliusAddressType;
use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerGuestType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AddressType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shippingAddress', SyliusAddressType::class, [
                'shippable' => true,
                'constraints' => [new Valid()],
            ])
            ->add('billingAddress', SyliusAddressType::class, [
                'constraints' => [new Valid()],
            ])
            ->add('differentBillingAddress', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'sylius.form.checkout.addressing.different_billing_address',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                $form = $event->getForm();
                $resource = $event->getData();
                $customer = $options['customer'];

                Assert::isInstanceOf($resource, CustomerAwareInterface::class);
                /** @var CustomerInterface $resourceCustomer */
                $resourceCustomer = $resource->getCustomer();

                if (
                    (null === $customer && null === $resourceCustomer) ||
                    (null !== $resourceCustomer && null === $resourceCustomer->getUser())
                ) {
                    $form->add('customer', CustomerGuestType::class, ['constraints' => [new Valid()]]);
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $orderData = $event->getData();

                if (isset($orderData['shippingAddress']) && (!isset($orderData['differentBillingAddress']) || false === $orderData['differentBillingAddress'])) {
                    $orderData['billingAddress'] = $orderData['shippingAddress'];

                    $event->setData($orderData);
                }
            })
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
    public function getBlockPrefix()
    {
        return 'sylius_checkout_address';
    }
}
