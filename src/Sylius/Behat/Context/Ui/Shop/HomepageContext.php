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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\Shop\MenuElementInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Webmozart\Assert\Assert;

final class HomepageContext implements Context
{
    /** @var HomePageInterface */
    private $homePage;

    /** @var MenuElementInterface */
    private $menuElement;

    public function __construct(HomePageInterface $homePage, MenuElementInterface $menuElement)
    {
        $this->homePage = $homePage;
        $this->menuElement = $menuElement;
    }

    /**
     * @When I check latest products
     * @When I visit the homepage
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
     * @Then I should see :firstMenuItem and :secondMenuItem in the menu
     */
    public function iShouldSeeAndInTheMenu(string ...$menuItems): void
    {
        Assert::allOneOf($menuItems, $this->menuElement->getMenuItems());
    }

    /**
     * @Then I should not see :firstMenuItem and :secondMenuItem in the menu
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
}
