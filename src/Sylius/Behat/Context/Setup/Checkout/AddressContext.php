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

namespace Sylius\Behat\Context\Setup\Checkout;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Sylius\Behat\Service\Factory\AddressFactoryInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Addressing\Converter\CountryNameConverterInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class AddressContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private MessageBusInterface $commandBus,
        private AddressFactoryInterface $addressFactory,
        private CountryNameConverterInterface $countryNameConverter,
    ) {
    }

    #[Given('I addressed the cart')]
    #[Given('the customer addressed the cart')]
    public function iAddressedTheCart(): void
    {
        $this->addressCart();
    }

    #[Given('I addressed the cart with :provinceName province')]
    public function iAddressedTheCartWithProvince(string $provinceName): void
    {
        $this->addressCart(
            billingAddress: $this->addressFactory->createDefaultWithProvinceName($provinceName),
        );
    }

    #[Given('/^I addressed the cart with "([^"]+)" as the billing address$/')]
    public function iAddressedTheCartWithBillingAddress(
        string $billingFullName,
    ): void {
        $this->addressCart(
            billingAddress: $this->addressFactory->createDefaultWithFirstAndLastName(...explode(' ', $billingFullName)),
        );
    }

    #[Given('/^I addressed the cart with "([^"]+)" as the shipping address$/')]
    public function iAddressedTheCartWithShippingAddress(
        string $shippingFullName,
    ): void {
        $this->addressCart(
            shippingAddress: $this->addressFactory->createDefaultWithFirstAndLastName(...explode(' ', $shippingFullName)),
        );
    }

    #[Given('/^I addressed the cart with "([^"]+)" as the billing address and "([^"]+)" as the shipping address$/')]
    public function iAddressedTheCartWithBillingAndShippingAddress(
        string $billingFullName,
        string $shippingFullName,
    ): void {
        $this->addressCart(
            billingAddress: $this->addressFactory->createDefaultWithFirstAndLastName(...explode(' ', $billingFullName)),
            shippingAddress: $this->addressFactory->createDefaultWithFirstAndLastName(...explode(' ', $shippingFullName)),
        );
    }

    #[Given('/^I addressed the cart with email "([^"]+)"$/')]
    #[Given('/^the (?:customer|visitor) addressed the cart with email "([^"]+)"$/')]
    public function iAddressedTheCartWithEmail(string $email): void
    {
        $this->addressCart(email: $email);
    }

    #[Given('I addressed the cart to :countryName')]
    public function iAddressedTheCartToCountry(string $countryName): void
    {
        $this->addressCart(
            billingAddress: $this->addressFactory->createDefaultWithCountryCode(
                $this->countryNameConverter->convertToCode($countryName),
            ),
        );
    }

    #[Given('/^I have specified the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/')]
    public function iHaveSpecifiedDefaultBillingAddressForName(): void
    {
        $this->addressCart(
            billingAddress: $this->addressFactory->createDefaultWithCountryCode('US'),
        );
    }

    public function addressCart(
        ?string $cartToken = null,
        ?string $email = null,
        ?AddressInterface $billingAddress = null,
        ?AddressInterface $shippingAddress = null,
    ): void {
        if ($email === null && $this->sharedStorage->has('user')) {
            $email = $this->sharedStorage->get('user')->getEmail();
        }

        $billingAddress = $billingAddress ?? $shippingAddress ?? $this->addressFactory->createDefault();
        $shippingAddress = $shippingAddress ?? $billingAddress ?? $this->addressFactory->createDefault();

        $this->commandBus->dispatch(new UpdateCart(
            orderTokenValue: $cartToken ?? $this->sharedStorage->get('cart_token'),
            email: $email,
            billingAddress: $billingAddress,
            shippingAddress: $shippingAddress,
        ));

        $this->storeAddresses($billingAddress, $shippingAddress);
    }

    private function storeAddresses(
        ?AddressInterface $billingAddress = null,
        ?AddressInterface $shippingAddress = null,
    ): void {
        if ($billingAddress === null && $shippingAddress !== null) {
            $billingAddress = clone $shippingAddress;
        }

        if ($shippingAddress === null && $billingAddress !== null) {
            $shippingAddress = clone $billingAddress;
        }

        $billingKey = sprintf(
            'billing_address_%s',
            StringInflector::nameToLowercaseCode($billingAddress->getFirstName() . ' ' . $billingAddress->getLastName()),
        );
        $this->sharedStorage->set($billingKey, $billingAddress);

        $shippingKey = sprintf(
            'shipping_address_%s',
            StringInflector::nameToLowercaseCode($shippingAddress->getFirstName() . ' ' . $shippingAddress->getLastName()),
        );
        $this->sharedStorage->set($shippingKey, $shippingAddress);
    }
}
