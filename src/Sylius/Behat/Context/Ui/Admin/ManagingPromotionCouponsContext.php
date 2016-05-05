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
use Sylius\Behat\Page\Admin\PromotionCoupon\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ManagingPromotionCouponsContext implements Context
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
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
    }

    /**
     * @Given /^I want to see all related coupons to (this promotion)$/
     */
    public function iWantToSeeAllRelatedCouponsToThisPromotion(PromotionInterface $promotion)
    {
        $this->indexPage->open(['promotionId' => $promotion->getId()]);
    }

    /**
     * @Given /^I want to create a new coupon for (this promotion)$/
     */
    public function iWantToCreateANewCouponForThisPromotion(PromotionInterface $promotion)
    {
        $this->createPage->open(['promotionId' => $promotion->getId()]);
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs($code)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When /^I set its usage limit to (\d+)$/
     */
    public function iSetItsUsageLimitTo($limit)
    {
        $this->createPage->setUsageLimit($limit);
    }

    /**
     * @When /^I set its per customer usage limit to (\d+)$/
     */
    public function iSetItsPerCustomerUsageLimitTo($limit)
    {
        $this->createPage->setCustomerUsageLimit($limit);
    }

    /**
     * @When I make it available till :date
     */
    public function iMakeItAvailableTill(\DateTime $date)
    {
        $this->createPage->setExpiresAt($date);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then /^I should see (\d+) coupon on the list$/
     */
    public function iShouldSeeCouponOnTheList($number)
    {
        Assert::eq(
            $number,
            $this->indexPage->countItems(),
            sprintf('There should be %s coupons but is %s', $number, $this->indexPage->countItems())
        );
    }

    /**
     * @Then /^I should see the coupon with code "([^"]*)"$/
     */
    public function iShouldSeeTheCouponWithCode($code)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['code' => $code]),
            sprintf('There should be coupon with code %s but it is not.', $code)
        );
    }
}
