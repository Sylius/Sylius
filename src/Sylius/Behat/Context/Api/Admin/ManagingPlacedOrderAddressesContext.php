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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class ManagingPlacedOrderAddressesContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I want to modify a customer's billing address of this order
     */
    public function iWantToModifyCustomerBillingAddress(): void
    {
        $this->client->buildUpdateRequest(
            Resources::ADDRESSES,
            (string) $this->sharedStorage->get('order')->getBillingAddress()->getId(),
        );
    }

    /**
     * @When /^I want to modify a customer's shipping address of this order$/
     */
    public function iWantToModifyCustomerAddress(): void
    {
        $this->client->buildUpdateRequest(
            Resources::ADDRESSES,
            (string) $this->sharedStorage->get('order')->getShippingAddress()->getId(),
        );
    }

    /**
     * @When /^I specify their (?:|new )(?:billing|shipping) (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     */
    public function iSpecifyTheirAddressAs(AddressInterface $address): void
    {
        $this->client->addRequestData('firstName', $address->getFirstName());
        $this->client->addRequestData('lastName', $address->getLastName());
        $this->client->addRequestData('street', $address->getStreet());
        $this->client->addRequestData('postcode', $address->getPostcode());
        $this->client->addRequestData('city', $address->getCity());
    }

    /**
     * @Then /^this order should(?:| still) have ("([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" as its(?:| new) billing address)$/
     */
    public function itsBillingAddressShouldContain(AddressInterface $address): void
    {
        $response = $this->client->show(
            Resources::ADDRESSES,
            (string) $this->sharedStorage->get('order')->getBillingAddress()->getId(),
        );

        $this->assertAddressResponseProperties($response, $address);
    }

    /**
     * @Then /^this order should(?:|still ) (be shipped to "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
     */
    public function itShouldBeShippedTo(AddressInterface $address): void
    {
        $response = $this->client->show(
            Resources::ADDRESSES,
            (string) $this->sharedStorage->get('order')->getShippingAddress()->getId(),
        );

        $this->assertAddressResponseProperties($response, $address);
    }

    private function assertAddressResponseProperties(Response $response, AddressInterface $exceptedAddress): void
    {
        $addressProperties = [
            'firstName' => 'getFirstName',
            'lastName' => 'getLastName',
            'street' => 'getStreet',
            'postcode' => 'getPostcode',
            'city' => 'getCity',
            'countryCode' => 'getCountryCode',
        ];

        foreach ($addressProperties as $property => $getter) {
            Assert::same($this->responseChecker->getValue($response, $property), $exceptedAddress->$getter());
        }
    }
}
