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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Webmozart\Assert\Assert;
use function Symfony\Component\DependencyInjection\Loader\Configurator\iterator;

final class ManagingShippingCategoriesContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    public function __construct(ApiClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @Given I want to create a new shipping category
     */
    public function iWantToCreateANewShippingCategory(): void
    {
        $this->client->buildCreateRequest('shipping_categories');
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I delete shipping category :shippingCategory
     */
    public function iDeleteShippingCategory(ShippingCategoryInterface $shippingCategory): void
    {
        $this->client->delete('shipping_categories', (string) $shippingCategory->getId());
    }

    /**
     * @When I browse shipping categories
     */
    public function iWantToBrowseShippingCategories(): void
    {
        $this->client->index('shipping_categories');
    }

    /**
     * @When I check (also) the :shippingCategoryName shipping category
     */
    public function iCheckTheShippingCategory(string $shippingCategoryName): void
    {
        Assert::true($this->client->hasItemWithValue('name', $shippingCategoryName));
    }

    /**
     * @When I do not specify its code
     * @When I specify its code as :shippingCategoryCode
     */
    public function iSpecifyItsCodeAs(string $shippingCategoryCode = ''): void
    {
        if ($shippingCategoryCode !== '') {
            $this->client->addRequestData('code', $shippingCategoryCode);
        }
    }

    /**
     * @When I name it :shippingCategoryName
     * @When I do not specify its name
     */
    public function iNameIt(string $shippingCategoryName = ''): void
    {
        if ($shippingCategoryName !== '') {
            $this->client->addRequestData('name', $shippingCategoryName);
        }
    }

    /**
     * @When I specify its description as :shippingCategoryDescription
     */
    public function iSpecifyItsDescriptionAs(string $shippingCategoryDescription): void
    {
        $this->client->addRequestData('description', $shippingCategoryDescription);
    }

    /**
     * @Then I should be notified that shipping category with this code already exists
     */
    public function iShouldBeNotifiedThatShippingCategoryWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->client->getError(), 'code: The shipping category with given code already exists.');
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatCodeIsRequired(string $element): void
    {
        Assert::same(
            $this->client->getError(), sprintf('%s: Please enter shipping category %s.', $element, $element)
        );
    }

    /**
     * @Then I should see a single shipping category in the list
     * @Then I should see :numberOfShippingCategories shipping categories in the list
     */
    public function iShouldSeeShippingCategoriesInTheList(int $numberOfShippingCategories = 1): void
    {
        $this->client->index('shipping_categories');
        Assert::same($this->client->countCollectionItems(), $numberOfShippingCategories);
    }

    /**
     * @Then /^the (shipping category "([^"]+)") should be in the registry$/
     * @Then /^the (shipping category "([^"]+)") should appear in the registry$/
     */
    public function theShippingCategoryShouldAppearInTheRegistry(ShippingCategoryInterface $shippingCategory): void
    {
        $this->client->index('shipping_categories');
        Assert::true($this->client->hasItemWithValue('name', $shippingCategory->getName()));
    }

    /**
     * @Then /^(this shipping category) should no longer exist in the registry$/
     */
    public function thisShippingCategoryShouldNoLongerExistInTheRegistry(ShippingCategoryInterface $shippingCategory): void
    {
        $this->client->index('shipping_categories');
        Assert::false($this->client->hasItemWithValue('name', $shippingCategory->getName()));
    }

    /**
     * @Then shipping category with name :shippingCategoryName should not be added
     */
    public function shippingCategoryWithNameShouldNotBeAdded(string $shippingCategoryName): void
    {
        $this->client->index('shipping_categories');
        Assert::false($this->client->hasItemWithValue('name', $shippingCategoryName));
    }

    /**
     * @Then there should still be only one shipping category with code :code
     */
    public function thereShouldStillBeOnlyOneShippingCategoryWith(string $code): void
    {
        $this->client->index('shipping_categories');
        Assert::same(count($this->client->getCollectionItemsWithValue('code', $code)), 1);
    }
}
