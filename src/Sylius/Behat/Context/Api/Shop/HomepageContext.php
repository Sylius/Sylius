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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class HomepageContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private string $apiUrlPrefix,
    ) {
    }

    /**
     * @When I check latest products
     */
    public function iCheckLatestProducts(): void
    {
        $this->client->customAction(
            sprintf('%s/shop/products?itemsPerPage=3&order[createdAt]=desc', $this->apiUrlPrefix),
            HttpRequest::METHOD_GET,
        );
    }

    /**
     * @Then I should see :productName product
     */
    public function iShouldSeeProduct(string $productName): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue(
                $this->client->getLastResponse(),
                'name',
                $productName,
            ),
        );
    }

    /**
     * @Then I should not see :productName product
     */
    public function iShouldNotSeeProduct(string $productName): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue(
                $this->client->getLastResponse(),
                'name',
                $productName,
            ),
        );
    }

    /**
     * @When I check available taxons
     */
    public function iCheckAvailableTaxons(): void
    {
        $this->client->customAction(sprintf('%s/shop/taxons', $this->apiUrlPrefix), HttpRequest::METHOD_GET);
    }

    /**
     * @Then I should see :count products in the list
     */
    public function iShouldSeeProductsInTheList(int $count): void
    {
        Assert::eq($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then I should see :firstMenuItem in the menu
     * @Then I should see :firstMenuItem and :secondMenuItem in the menu
     */
    public function iShouldSeeAndInTheMenu(string ...$expectedMenuItems): void
    {
        $response = json_decode($this->client->getLastResponse()->getContent(), true);
        Assert::keyExists($response, 'hydra:member');
        $menuItems = array_column($response['hydra:member'], 'name');

        Assert::notEmpty($menuItems);
        Assert::allOneOf($menuItems, $expectedMenuItems);
    }

    /**
     * @Then I should not see :firstMenuItem and :secondMenuItem in the menu
     * @Then I should not see :firstMenuItem, :secondMenuItem and :thirdMenuItem in the menu
     * @Then I should not see :firstMenuItem, :secondMenuItem, :thirdMenuItem and :fourthMenuItem in the menu
     */
    public function iShouldNotSeeAndInTheMenu(string ...$unexpectedMenuItems): void
    {
        $response = json_decode($this->client->getLastResponse()->getContent(), true);
        $menuItems = array_column($response, 'name');

        foreach ($unexpectedMenuItems as $unexpectedMenuItem) {
            if (in_array($unexpectedMenuItem, $menuItems, true)) {
                throw new \InvalidArgumentException(sprintf('There is menu item %s but it should not be', $unexpectedMenuItem));
            }
        }
    }
}
