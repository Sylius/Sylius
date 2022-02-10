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

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType as SyliusAddressType;
use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerCheckoutGuestType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Addressing\Comparator\AddressComparatorInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

final class AddressType extends AbstractResourceType
{
    private ?AddressComparatorInterface $addressComparator;

    public function __construct(string $dataClass, array $validationGroups = [], ?AddressComparatorInterface $addressComparator = null)
    {
        parent::__construct($dataClass, $validationGroups);

        if (null === $addressComparator) {
            @trigger_error(
                sprintf(
                    'Not passing an $addressComparator to "%s" constructor is deprecated since Sylius 1.8 and will be impossible in Sylius 2.0.',
                    __CLASS__,
                ),
                \E_USER_DEPRECATED
            );
        }

        $this->addressComparator = $addressComparator;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('differentBillingAddress', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'sylius.form.checkout.addressing.different_billing_address',
            ])
            ->add('differentShippingAddress', CheckboxType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'sylius.form.checkout.addressing.different_shipping_address',
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event): void {
                $form = $event->getForm();

                Assert::isInstanceOf($event->getData(), OrderInterface::class);

                /** @var OrderInterface $order */
                $order = $event->getData();
                $channel = $order->getChannel();

                $form
                    ->add('shippingAddress', SyliusAddressType::class, [
                        'shippable' => true,
                        'constraints' => [new Valid()],
                        'channel' => $channel,
                    ])
                    ->add('billingAddress', SyliusAddressType::class, [
                        'constraints' => [new Valid()],
                        'channel' => $channel,
                    ])
                ;
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
                $form = $event->getForm();

                Assert::isInstanceOf($event->getData(), OrderInterface::class);

                /** @var OrderInterface $order */
                $order = $event->getData();
                $areAddressesDifferent = $this->areAddressesDifferent($order->getBillingAddress(), $order->getShippingAddress());

                $form->get('differentBillingAddress')->setData($areAddressesDifferent);
                $form->get('differentShippingAddress')->setData($areAddressesDifferent);
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options): void {
                $form = $event->getForm();
                $resource = $event->getData();
                $customer = $options['customer'];

                Assert::isInstanceOf($resource, CustomerAwareInterface::class);

                /** @var CustomerInterface|null $resourceCustomer */
                $resourceCustomer = $resource->getCustomer();

                if (
                    (null === $customer && null === $resourceCustomer) ||
                    (null !== $resourceCustomer && null === $resourceCustomer->getUser()) ||
                    ($resourceCustomer !== $customer)
                ) {
                    $form->add('customer', CustomerCheckoutGuestType::class, ['constraints' => [new Valid()]]);
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
                $orderData = $event->getData();

                $differentBillingAddress = $orderData['differentBillingAddress'] ?? false;
                $differentShippingAddress = $orderData['differentShippingAddress'] ?? false;

                if (isset($orderData['billingAddress']) && !$differentBillingAddress && !$differentShippingAddress) {
                    $orderData['shippingAddress'] = $orderData['billingAddress'];
                }

                if (isset($orderData['shippingAddress']) && !$differentBillingAddress && !$differentShippingAddress) {
                    $orderData['billingAddress'] = $orderData['shippingAddress'];
                }

                $event->setData($orderData);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'customer' => null,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_checkout_address';
    }

    private function areAddressesDifferent(?AddressInterface $firstAddress, ?AddressInterface $secondAddress): bool
    {
        if (null === $this->addressComparator || null === $firstAddress || null === $secondAddress) {
            return false;
        }

        return !$this->addressComparator->equal($firstAddress, $secondAddress);
    }
}
