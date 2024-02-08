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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\Shop\MenuElementInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Webmozart\Assert\Assert;

final class HomepageContext implements Context
{
    public function __construct(
        private HomePageInterface $homePage,
        private MenuElementInterface $menuElement,
    ) {
    }

    /**
     * @When I check latest products
     * @When I check available taxons
     */
    public function iCheckLatestProducts(): void
    {
        $this->homePage->open();
    }

    /**
     * @Then I should be redirected to the homepage
     */
    public function iShouldBeRedirectedToTheHomepage(): void
    {
        $this->homePage->verify();
    }

    /**
     * @Then I should see :numberOfProducts products in the list
     */
    public function iShouldSeeProductsInTheList(int $numberOfProducts): void
    {
        Assert::same(count($this->homePage->getLatestProductsNames()), $numberOfProducts);
    }

    /**
     * @Then I should see :productName product
     */
    public function iShouldSeeProduct(string $productName): void
    {
        Assert::inArray($productName, $this->homePage->getLatestProductsNames());
    }

    /**
     * @Then I should not see :productName product
     */
    public function iShouldNotSeeProduct(string $productName): void
    {
        Assert::true(!in_array($productName, $this->homePage->getLatestProductsNames()));
    }

    /**
     * @Then I should see :firstMenuItem in the menu
     * @Then I should see :firstMenuItem and :secondMenuItem in the menu
     */
    public function iShouldSeeAndInTheMenu(string ...$menuItems): void
    {
        Assert::allOneOf($menuItems, $this->menuElement->getMenuItems());
    }

    /**
     * @Then I should not see :firstMenuItem and :secondMenuItem in the menu
     * @Then I should not see :firstMenuItem, :secondMenuItem and :thirdMenuItem in the menu
     * @Then I should not see :firstMenuItem, :secondMenuItem, :thirdMenuItem and :fourthMenuItem in the menu
     */
    public function iShouldNotSeeAndInTheMenu(string ...$menuItems): void
    {
        $actualMenuItems = $this->menuElement->getMenuItems();
        foreach ($menuItems as $menuItem) {
            if (in_array($menuItem, $actualMenuItems)) {
                throw new \InvalidArgumentException(sprintf('Menu should not contain %s element', $menuItem));
            }
        }
    }

    /**
     * @Then I should be logged in
     */
    public function iShouldBeLoggedIn(): void
    {
        $this->homePage->verify();
        Assert::true($this->homePage->hasLogoutButton());
    }
}
