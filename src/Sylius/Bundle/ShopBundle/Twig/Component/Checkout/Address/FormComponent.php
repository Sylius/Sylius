<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address;

use ArrayObject;
use Sylius\Bundle\ShopBundle\Event\CheckoutAddressUpdatedEvent;
use Sylius\Bundle\ShopBundle\ShopEvents;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;

#[AsLiveComponent]
class FormComponent
{
    /** @use ResourceFormComponentTrait<OrderInterface> */
    use ResourceFormComponentTrait;

    use TemplatePropTrait;

    #[LiveProp]
    public bool $emailExists = false;

    /**
     * @param OrderRepositoryInterface<OrderInterface> $repository
     * @param UserRepositoryInterface<ShopUserInterface> $shopUserRepository
     */
    public function __construct(
        OrderRepositoryInterface $repository,
        FormFactoryInterface $formFactory,
        string $resourceClass,
        string $formClass,
        protected readonly CustomerContextInterface $customerContext,
        protected readonly UserRepositoryInterface $shopUserRepository,
        protected readonly AddressRepositoryInterface $addressRepository,
        protected readonly ?EventDispatcherInterface $eventDispatcher = null,
    ) {
        $this->initialize($repository, $formFactory, $resourceClass, $formClass);

        if (null === $this->eventDispatcher) {
            trigger_deprecation(
                'sylius/shop-bundle',
                '2.2',
                'Not passing an $eventDispatcher to the constructor of %s is deprecated and will be required in Sylius 3.0.',
                self::class,
            );
        }
    }

    #[PreReRender(priority: -100)]
    public function checkEmailExist(): void
    {
        $email = $this->formValues['customer']['email'] ?? null;
        if (null !== $email) {
            $this->emailExists = $this->shopUserRepository->findOneByEmail($email) !== null;
        }
    }

    #[LiveListener(AddressBookComponent::SYLIUS_SHOP_ADDRESS_UPDATED)]
    public function addressFieldUpdated(#[LiveArg] mixed $addressId, #[LiveArg] string $field): void
    {
        $address = $this->addressRepository->find($addressId);

        $newAddress = [];
        $newAddress['firstName'] = $address->getFirstName();
        $newAddress['lastName'] = $address->getLastName();
        $newAddress['phoneNumber'] = $address->getPhoneNumber();
        $newAddress['company'] = $address->getCompany();
        $newAddress['countryCode'] = $address->getCountryCode();
        if ($address->getProvinceCode() !== null) {
            $newAddress['provinceCode'] = $address->getProvinceCode();
        }
        if ($address->getProvinceName() !== null) {
            $newAddress['provinceName'] = $address->getProvinceName();
        }
        $newAddress['street'] = $address->getStreet();
        $newAddress['city'] = $address->getCity();
        $newAddress['postcode'] = $address->getPostcode();

        $formData = new ArrayObject($newAddress);

        $this->eventDispatcher?->dispatch(
            new CheckoutAddressUpdatedEvent($formData, $address),
            ShopEvents::CHECKOUT_ADDRESS_UPDATED,
        );

        $this->formValues[$field] = (array) $formData;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create(
            $this->formClass,
            $this->resource,
            ['customer' => $this->customerContext->getCustomer()],
        );
    }
}
