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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class AddressContext implements Context
{
    /** @param AddressFactoryInterface<AddressInterface> $addressFactory */
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private MessageBusInterface $commandBus,
        private AddressFactoryInterface $addressFactory,
    ) {
    }

    /**
     * @Given I addressed the cart
     * @Given I addressed it
     */
    public function iAddressedTheCart(): void
    {
        $cartToken = $this->sharedStorage->get('cart_token');

        $command = new UpdateCart(email: null, billingAddress: $this->getDefaultAddress(), orderTokenValue: $cartToken);
        $this->commandBus->dispatch($command);
    }

    /**
     * @Given /^I have specified the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iHaveSpecifiedDefaultBillingAddressForName(): void
    {
        $cartToken = $this->sharedStorage->get('cart_token');

        $command = new UpdateCart(email: null, billingAddress: $this->getDefaultAddress(), orderTokenValue: $cartToken);
        $this->commandBus->dispatch($command);
    }

    private function getDefaultAddress(): AddressInterface
    {
        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();

        $address->setCity('New York');
        $address->setStreet('Wall Street');
        $address->setPostcode('00-001');
        $address->setCountryCode('US');
        $address->setFirstName('Richy');
        $address->setLastName('Rich');

        return $address;
    }
}
