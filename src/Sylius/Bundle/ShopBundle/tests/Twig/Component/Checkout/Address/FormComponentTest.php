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

namespace Tests\Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Modifier\DefaultAddressFormValuesModifier;
use Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\FormComponent;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;

final class FormComponentTest extends TestCase
{
    private CustomerContextInterface&MockObject $customerContext;

    private AddressRepositoryInterface&MockObject $addressRepository;

    private FormComponent $formComponent;

    protected function setUp(): void
    {
        $this->customerContext = $this->createMock(CustomerContextInterface::class);
        $this->addressRepository = $this->createMock(AddressRepositoryInterface::class);

        $this->formComponent = new FormComponent(
            $this->createMock(OrderRepositoryInterface::class),
            $this->createMock(FormFactoryInterface::class),
            OrderInterface::class,
            'form_type',
            $this->customerContext,
            $this->createMock(UserRepositoryInterface::class),
            $this->addressRepository,
            [new DefaultAddressFormValuesModifier()],
        );
    }

    public function testFillsTheFormWithAnAddressFromTheCustomerAddressBook(): void
    {
        $address = $this->createAddress(1);
        $this->customerContext->method('getCustomer')->willReturn($this->createCustomer($address));
        $this->addressRepository->expects($this->never())->method('findOneByCustomer');

        $this->formComponent->formValues = ['shippingAddress' => []];
        $this->formComponent->addressFieldUpdated('1', 'shippingAddress');

        $this->assertSame(
            [
                'firstName' => 'Lucius',
                'lastName' => 'Fox',
                'phoneNumber' => '555-0100',
                'company' => 'Wayne Enterprises',
                'countryCode' => 'US',
                'street' => '1007 Mountain Drive',
                'city' => 'Gotham',
                'postcode' => '53701',
            ],
            $this->formComponent->formValues['shippingAddress'],
        );
    }

    public function testDoesNothingWhenTheAddressIsNotInTheCustomerAddressBook(): void
    {
        $this->customerContext->method('getCustomer')->willReturn($this->createCustomer($this->createAddress(1)));

        $this->formComponent->formValues = ['shippingAddress' => []];
        $this->formComponent->addressFieldUpdated('2', 'shippingAddress');

        $this->assertSame([], $this->formComponent->formValues['shippingAddress']);
    }

    public function testDoesNothingWhenThereIsNoCustomer(): void
    {
        $this->customerContext->method('getCustomer')->willReturn(null);

        $this->formComponent->formValues = ['shippingAddress' => []];
        $this->formComponent->addressFieldUpdated('1', 'shippingAddress');

        $this->assertSame([], $this->formComponent->formValues['shippingAddress']);
    }

    public function testWorksWithoutTheDeprecatedAddressRepository(): void
    {
        $address = $this->createAddress(1);
        $this->customerContext->method('getCustomer')->willReturn($this->createCustomer($address));

        $formComponent = new FormComponent(
            $this->createMock(OrderRepositoryInterface::class),
            $this->createMock(FormFactoryInterface::class),
            OrderInterface::class,
            'form_type',
            $this->customerContext,
            $this->createMock(UserRepositoryInterface::class),
            null,
            [new DefaultAddressFormValuesModifier()],
        );

        $formComponent->formValues = ['shippingAddress' => []];
        $formComponent->addressFieldUpdated('1', 'shippingAddress');

        $this->assertSame('1007 Mountain Drive', $formComponent->formValues['shippingAddress']['street']);
    }

    public function testDoesNothingWhenTheAddressIdentifierIsNotScalar(): void
    {
        $this->customerContext->method('getCustomer')->willReturn($this->createCustomer($this->createAddress(1)));

        $this->formComponent->formValues = ['shippingAddress' => []];
        $this->formComponent->addressFieldUpdated(['1'], 'shippingAddress');

        $this->assertSame([], $this->formComponent->formValues['shippingAddress']);
    }

    private function createCustomer(AddressInterface ...$addresses): CustomerInterface&MockObject
    {
        $customer = $this->createMock(CustomerInterface::class);
        $customer->method('getAddresses')->willReturn(new ArrayCollection($addresses));

        return $customer;
    }

    private function createAddress(int $id): AddressInterface&MockObject
    {
        $address = $this->createMock(AddressInterface::class);
        $address->method('getId')->willReturn($id);
        $address->method('getFirstName')->willReturn('Lucius');
        $address->method('getLastName')->willReturn('Fox');
        $address->method('getPhoneNumber')->willReturn('555-0100');
        $address->method('getCompany')->willReturn('Wayne Enterprises');
        $address->method('getCountryCode')->willReturn('US');
        $address->method('getProvinceCode')->willReturn(null);
        $address->method('getProvinceName')->willReturn(null);
        $address->method('getStreet')->willReturn('1007 Mountain Drive');
        $address->method('getCity')->willReturn('Gotham');
        $address->method('getPostcode')->willReturn('53701');

        return $address;
    }
}
