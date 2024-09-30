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

namespace Sylius\Behat\Context\Api\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final readonly class CheckoutShippingContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Then I should see that there is no assigned shipping method
     */
    public function iShouldSeeThatThereIsNoAssignedShippingMethod(): void
    {
        $response = $this->client->requestGet(sprintf('orders/%s', $this->sharedStorage->get('cart_token')));

        Assert::isEmpty($this->responseChecker->getValue($response, 'shipments'));
    }

    /**
     * @Then there should not be any shipping method available to choose
     */
    public function thereShouldNotBeAnyShippingMethodAvailableToChoose(): void
    {
        $response = $this->client->requestGet('shipping-methods');

        Assert::isEmpty($this->responseChecker->getCollection($response));
    }
}
