<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Promotion\CreatePageInterface;
use Sylius\Behat\Page\Admin\Promotion\IndexPageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ManagingPromotionsContext implements Context
{
    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     */
    public function __construct(CreatePageInterface $createPage, IndexPageInterface $indexPage)
    {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
    }

    /**
     * @Given I want to create a new promotion
     */
    public function iWantToCreateANewPromotion()
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs($code)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I name it :name
     */
    public function iNameIt($name)
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @Then the promotion :promotionName should appear in the registry
     */
    public function thePromotionShouldAppearInTheRegistry($promotionName)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isResourceOnPage(['name' => $promotionName]),
            sprintf('Promotion with name %s has not been found.', $promotionName)
        );
    }
}
