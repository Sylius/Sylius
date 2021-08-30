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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Webmozart\Assert\Assert;

final class ManagingCatalogPromotionsContext implements Context
{
    private IndexPageInterface $indexPage;

    public function __construct(IndexPageInterface $indexPage)
    {
        $this->indexPage = $indexPage;
    }

    /**
     * @When I browse catalog promotions
     */
    public function iBrowseCatalogPromotions(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then there should be :amount catalog promotions on the list
     */
    public function thereShouldBeCatalogPromotionsOnTheList(int $amount): void
    {
        Assert::same($this->indexPage->countItems(), $amount);
    }

    /**
     * @Then the catalog promotions named :firstName and :secondName should be in the registry
     */
    public function theCatalogPromotionsNamedShouldBeInTheRegistry(string ...$names): void
    {
        foreach ($names as $name) {
            Assert::true(
                $this->indexPage->isSingleResourceOnPage(['name' => $name]),
                sprintf('Cannot find catalog promotions with name "%s" in the list', $name)
            );
        }
    }
}
