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

use Sylius\Bundle\ShopBundle\Modifier\AddressFormValuesModifierInterface;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponentTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
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
        /** @var iterable<AddressFormValuesModifierInterface> */
        protected readonly ?iterable $addressFormValuesModifiers = null,
    ) {
        $this->initialize($repository, $formFactory, $resourceClass, $formClass);
        if (null === $this->addressFormValuesModifiers) {
            trigger_deprecation(
                'sylius/shop-bundle',
                '2.2',
                'Not passing a "%s" to "%s" is deprecated and will be required in Sylius 3.0.',
                AddressFormValuesModifierInterface::class,
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
        $customer = $this->customerContext->getCustomer();
        if (!$customer instanceof CustomerInterface) {
            return;
        }

        $address = $this->addressRepository->findOneByCustomer((string) $addressId, $customer);
        if (null === $address) {
            return;
        }

        $newAddress = [];
        if (null === $this->addressFormValuesModifiers) {
            $newAddress = $this->createAddressArrayFromEntity($address);
        } else {
            foreach ($this->addressFormValuesModifiers as $modifier) {
                $newAddress = $modifier->modify($newAddress, $address);
            }
        }

        $this->formValues[$field] = $newAddress;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create(
            $this->formClass,
            $this->resource,
            ['customer' => $this->customerContext->getCustomer()],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function createAddressArrayFromEntity(AddressInterface $address): array
    {
        $newAddress = [
            'firstName' => $address->getFirstName(),
            'lastName' => $address->getLastName(),
            'phoneNumber' => $address->getPhoneNumber(),
            'company' => $address->getCompany(),
            'countryCode' => $address->getCountryCode(),
            'street' => $address->getStreet(),
            'city' => $address->getCity(),
            'postcode' => $address->getPostcode(),
        ];

        if (null !== $address->getProvinceCode()) {
            $newAddress['provinceCode'] = $address->getProvinceCode();
        }

        if (null !== $address->getProvinceName()) {
            $newAddress['provinceName'] = $address->getProvinceName();
        }

        return $newAddress;
    }
}
