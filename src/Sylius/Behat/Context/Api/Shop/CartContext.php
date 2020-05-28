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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Webmozart\Assert\Assert;

final class CartContext implements Context
{
    /** @var ApiClientInterface */
    private $cartsClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    public function __construct(ApiClientInterface $cartsClient, ResponseCheckerInterface $responseChecker)
    {
        $this->cartsClient = $cartsClient;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @When I see the summary of my cart
     */
    public function iSeeTheSummaryOfMyCart(): void
    {
        $this->cartsClient->create(Request::create('orders'));
    }

    /**
     * @Then my cart should be empty
     */
    public function myCartShouldBeEmpty(): void
    {
        $response = $this->cartsClient->getLastResponse();
        Assert::true(
            $this->responseChecker->isCreationSuccessful($response),
            'Cart has not been created. Reason: ' . $response->getContent()
        );
    }
}
