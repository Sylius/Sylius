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
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class OrderContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Then I should be able to access this order's details
     */
    public function iShouldBeAbleToAccessThisOrderDetails(): void
    {
        $response = $this->client->show($this->sharedStorage->get('cart_token'));

        Assert::same($response->getStatusCode(), Response::HTTP_OK);
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'checkoutState'),
            OrderCheckoutStates::STATE_COMPLETED
        );
        Assert::same(
            $this->sharedStorage->get('order_number'),
            $this->responseChecker->getValue($this->client->getLastResponse(), 'number')
        );
    }

    /**
     * @Then /^my tax total should be ("[^"]+")$/
     */
    public function myTaxTotalShouldBe(int $taxTotal): void
    {
        $response = $this->client->show($this->sharedStorage->get('cart_token'));

        $responseTaxTotal = $this->responseChecker->getValue($response, 'taxTotal');
        Assert::same($taxTotal, (int) $responseTaxTotal);
    }
}
