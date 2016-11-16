<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class HomepageContext implements Context
{
    /**
     * @var HomePageInterface
     */
    private $homePage;

    /**
     * @param HomePageInterface $homepage
     */
    public function __construct(HomePageInterface $homepage)
    {
        $this->homePage = $homepage;
    }

    /**
     * @When I check latest products
     */
    public function iCheckLatestProducts()
    {
        $this->homePage->open();
    }

    /**
     * @Then I should see :numberOfProducts products in the list
     */
    public function iShouldSeeProductsInTheList($numberOfProducts)
    {
        $foundProductsNames = $this->homePage->getLatestProductsNames();

        Assert::same(
            (int) $numberOfProducts,
            count($foundProductsNames),
            '%d rows with products should appear on page, %d rows has been found'
        );
    }
}
