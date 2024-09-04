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

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class FormComponent
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp]
    public ?Order $order = null;

    #[LiveProp]
    public bool $emailExists = false;

    /**
     * @param UserRepositoryInterface<ShopUserInterface> $shopUserRepository
     * @param class-string $formClass
     */
    public function __construct(
        private readonly CustomerContextInterface $customerContext,
        private readonly UserRepositoryInterface $shopUserRepository,
        private readonly AddressRepositoryInterface $addressRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly string $formClass,
    ) {
    }

    #[PreReRender(priority: -100)]
    public function checkEmailExist(): void
    {
        $email = $this->formValues['customer']['email'] ?? null;
        if (null !== $email) {
            $this->emailExists = $this->shopUserRepository->findOneByEmail($email) !== null;
        }
    }

    #[LiveListener('sylius:shop:address-updated')]
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

        $this->formValues[$field] = $newAddress;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->formFactory->create(
            $this->formClass,
            $this->order,
            ['customer' => $this->customerContext->getCustomer()],
        );
    }
}
