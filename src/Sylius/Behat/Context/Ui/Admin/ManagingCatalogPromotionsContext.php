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
use Sylius\Behat\Page\Admin\CatalogPromotion\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Webmozart\Assert\Assert;

final class ManagingCatalogPromotionsContext implements Context
{
    private IndexPageInterface $indexPage;

    private CreatePageInterface $createPage;

    public function __construct(IndexPageInterface $indexPage, CreatePageInterface $createPage)
    {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
    }

    /**
     * @When I browse catalog promotions
     */
    public function iBrowseCatalogPromotions(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I create a new catalog promotion with :code code and :name name
     */
    public function iCreateANewCatalogPromotionWithCodeAndName(string $code, string $name): void
    {
        $this->createPage->open();
        $this->createPage->specifyCode($code);
        $this->createPage->nameIt($name);
        $this->createPage->create();
    }

    /**
     * @Then there should be :amount catalog promotions on the list
     * @Then there should be :amount new catalog promotion on the list
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
    /**
     * @Then it should have :code code and :name name
     */
    public function itShouldHaveCodeAndName(string $code, string $name): void
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $name, 'code' => $code]),
            sprintf('Cannot find catalog promotions with code "%s" and name "%s" in the list', $code, $name)
        );
    }

    /**
     * @Then this catalog promotion should be usable
     */
    public function thisCatalogPromotionShouldBeUsable(): void
    {
        // Intentionally left blank
    }
}
