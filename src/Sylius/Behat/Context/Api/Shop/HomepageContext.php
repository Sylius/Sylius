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
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class HomepageContext implements Context
{
    /** @var ApiClientInterface */
    private $productsClient;

    /** @var ApiClientInterface */
    private $taxonsClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $productsClient,
        ApiClientInterface $taxonsClient,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->productsClient = $productsClient;
        $this->taxonsClient = $taxonsClient;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I check latest products
     */
    public function iCheckLatestProducts(): void
    {
        $this->productsClient->customAction('new-api/products/latest', HttpRequest::METHOD_GET);
    }

    /**
     * @When I check available taxons
     */
    public function iCheckAvailableTaxons(): void
    {
        $this->taxonsClient->customAction('new-api/taxons', HttpRequest::METHOD_GET);
    }

    /**
     * @Then I should see :count products in the list
     */
    public function iShouldSeeProductsInTheList(int $count): void
    {
        Assert::eq($this->responseChecker->countCollectionItems($this->productsClient->getLastResponse()), $count);
    }

    /**
     * @Then I should see :firstMenuItem and :secondMenuItem in the menu
     */
    public function iShouldSeeAndInTheMenu(string ...$expectedMenuItems): void
    {
        $response = json_decode($this->taxonsClient->getLastResponse()->getContent(), true);
        Assert::keyExists($response, 'hydra:member');
        $menuItems = array_column(array_column(array_column($response['hydra:member'], 'translations'), 'en_US'), 'name');

        Assert::notEmpty($menuItems);
        Assert::allOneOf($menuItems, $expectedMenuItems);
    }

    /**
     * @Then I should not see :firstMenuItem and :secondMenuItem in the menu
     */
    public function iShouldNotSeeAndInTheMenu(string ...$unexpectedMenuItems): void
    {
        $response = json_decode($this->taxonsClient->getLastResponse()->getContent(), true);
        $menuItems = array_column($response, 'name');

        foreach ($unexpectedMenuItems as $unexpectedMenuItem) {
            if (in_array($unexpectedMenuItem, $menuItems, true)) {
                throw new \InvalidArgumentException(sprintf('There is menu item %s but it should not be', $unexpectedMenuItem));
            }
        }
    }
}
