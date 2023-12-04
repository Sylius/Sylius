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

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class HomepageContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private ObjectManager $objectManager,
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
        $this->objectManager->clear(); // avoiding doctrine cache
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
        $menuItems = $this->getAvailableTaxonMenuItemsFromTaxonCollection($this->client->getLastResponse());

        Assert::true(
            $this->areAllMenuItemsVisible($menuItems, $expectedMenuItems),
            sprintf('Menu items %s should be present in the menu', implode(', ', $expectedMenuItems)),
        );
    }

    /**
     * @Then I should not see :firstMenuItem and :secondMenuItem in the menu
     * @Then I should not see :firstMenuItem, :secondMenuItem and :thirdMenuItem in the menu
     * @Then I should not see :firstMenuItem, :secondMenuItem, :thirdMenuItem and :fourthMenuItem in the menu
     */
    public function iShouldNotSeeAndInTheMenu(string ...$unexpectedMenuItems): void
    {
        $menuItems = $this->getAvailableTaxonMenuItemsFromTaxonCollection($this->client->getLastResponse());

        Assert::false(
            $this->areAllMenuItemsVisible($menuItems, $unexpectedMenuItems),
            sprintf('Menu items %s should not be present in the menu', implode(', ', $unexpectedMenuItems)),
        );
    }

    private function areAllMenuItemsVisible(array $menuItems, array $expectedMenuItems): bool
    {
        foreach ($expectedMenuItems as $expectedMenuItem) {
            if (!in_array($expectedMenuItem, $menuItems)) {
                return false;
            }
        }

        return true;
    }

    private function getAvailableTaxonMenuItemsFromTaxonCollection(Response $response): array
    {
        $taxons = $this->responseChecker->getCollection($response);
        if ([] === $taxons) {
            return [];
        }
        $menuItems = array_column($taxons, 'name');

        Assert::notEmpty($menuItems);

        $children = array_column($taxons, 'children');
        foreach ($children[0] as $child) {
            if (!empty($child)) {
                array_push($menuItems, $this->iriConverter->getResourceFromIri($child)->getName());
            }
        }

        return $menuItems;
    }
}
