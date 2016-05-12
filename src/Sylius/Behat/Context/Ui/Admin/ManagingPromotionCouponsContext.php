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
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\PromotionCoupon\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\PromotionCoupon\GeneratePageInterface;
use Sylius\Behat\Page\Admin\PromotionCoupon\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingPromotionCouponsContext implements Context
{
    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var GeneratePageInterface
     */
    private $generatePage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param CreatePageInterface $createPage
     * @param GeneratePageInterface $generatePage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        CreatePageInterface $createPage,
        GeneratePageInterface $generatePage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->createPage = $createPage;
        $this->generatePage = $generatePage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given /^I want to view all coupons of (this promotion)$/
     * @Given /^I want to view all coupons of ("[^"]+" promotion)$/
     */
    public function iWantToViewAllCouponsOfThisPromotion(PromotionInterface $promotion)
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
     * @Given /^I want to modify the ("[^"]+" coupon) for (this promotion)$/
     */
    public function iWantToModifyTheCoupon(CouponInterface $coupon, PromotionInterface $promotion)
    {
        $this->updatePage->open(['id' => $coupon->getId(), 'promotionId' => $promotion->getId()]);
    }

    /**
     * @Given /^I want to generate a new coupons for (this promotion)$/
     */
    public function iWantToGenerateANewCouponsForThisPromotion(PromotionInterface $promotion)
    {
        $this->generatePage->open(['promotionId' => $promotion->getId()]);
    }

    /**
     * @When /^I specify its code length as (\d+)$/
     * @When I do not specify its code length
     */
    public function iSpecifyItsCodeLengthAs($codeLength = null)
    {
        $this->generatePage->specifyCodeLength($codeLength);
    }

    /**
     * @When /^I limit generated coupons usage to (\d+) times$/
     */
    public function iSetGeneratedCouponsUsageLimitTo($limit)
    {
        $this->generatePage->setUsageLimit($limit);
    }

    /**
     * @When I make generated coupons valid until :date
     */
    public function iMakeGeneratedCouponsValidUntil(\DateTime $date)
    {
        $this->generatePage->setExpiresAt($date);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I limit its usage to :limit times
     */
    public function iLimitItsUsageLimitTo($limit)
    {
        $this->createPage->setUsageLimit($limit);
    }

    /**
     * @When I change its usage limit to :limit
     */
    public function iChangeItsUsageLimitTo($limit)
    {
        $this->updatePage->setUsageLimit($limit);
    }

    /**
     * @When I specify its amount as :amount
     * @When I do not specify its amount
     */
    public function iSpecifyItsAmountAs($amount = null)
    {
        $this->generatePage->specifyAmount($amount);
    }

    /**
     * @When I limit its per customer usage to :limit times
     */
    public function iLimitItsPerCustomerUsageLimitTo($limit)
    {
        $this->createPage->setCustomerUsageLimit($limit);
    }

    /**
     * @When I change its per customer usage limit to :limit
     */
    public function iChangeItsPerCustomerUsageLimitTo($limit)
    {
        $this->updatePage->setCustomerUsageLimit($limit);
    }

    /**
     * @When I make it valid until :date
     */
    public function iMakeItValidUntil(\DateTime $date)
    {
        $this->createPage->setExpiresAt($date);
    }

    /**
     * @When I change expires date to :date
     */
    public function iChangeExpiresDateTo(\DateTime $date)
    {
        $this->updatePage->setExpiresAt($date);
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
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I generate it
     * @When I try to generate it
     */
    public function iGenerateIt()
    {
        $this->generatePage->generate();
    }

    /**
     * @When /^I delete ("[^"]+" coupon) related to (this promotion)$/
     * @When /^I try to delete ("[^"]+" coupon) related to (this promotion)$/
     */
    public function iDeleteCouponRelatedToThisPromotion(CouponInterface $coupon, PromotionInterface $promotion)
    {
        $this->indexPage->open(['promotionId' => $promotion->getId()]);
        $this->indexPage->deleteResourceOnPage(['code' => $coupon->getCode()]);
    }

    /**
     * @Then /^there should be (\d+) coupon related to (this promotion)$/
     */
    public function thereShouldBeCouponRelatedTo($number, PromotionInterface $promotion)
    {
        $this->indexPage->open(['promotionId' => $promotion->getId()]);

        Assert::eq(
            $number,
            $this->indexPage->countItems(),
            'There should be %s coupons but is %s'
        );
    }

    /**
     * @Then /^there should be coupon with code "([^"]+)"$/
     */
    public function thereShouldBeCouponWithCode($code)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['code' => $code]),
            sprintf('There should be coupon with code %s but it is not.', $code)
        );
    }

    /**
     * @Then this coupon should be valid until :date
     */
    public function thisCouponShouldBeValidUntil(\DateTime $date)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['Expires at' => date('d-m-Y', $date->getTimestamp())]),
            sprintf('There should be coupon with expires date %s', date('d-m-Y', $date->getTimestamp()))
        );
    }

    /**
     * @Then /^this coupon should have (\d+) usage limit$/
     */
    public function thisCouponShouldHaveUsageLimit($limit)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['Usage limit' => $limit]),
            sprintf('There should be coupon with %s usage limit', $limit)
        );
    }

    /**
     * @Then /^this coupon should have (\d+) per customer usage limit$/
     */
    public function thisCouponShouldHavePerCustomerUsageLimit($limit)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['Per customer usage limit' => $limit]),
            sprintf('There should be coupon with %s per customer usage limit', $limit)
        );
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code field should be disabled'
        );
    }

    /**
     * @Then /^I should be notified that coupon with this code already exists$/
     */
    public function iShouldBeNotifiedThatCouponWithThisCodeAlreadyExists()
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor('code', 'This coupon already exists.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, sprintf('Please enter coupon %s.', $element)),
            sprintf('I should be notified that coupon %s should be required.', $element)
        );
    }

    /**
     * @Then I should be notified that generate amount is required
     */
    public function iShouldBeNotifiedThatGenerateAmountIsRequired()
    {
        Assert::true(
            $this->generatePage->checkAmountValidation('Please enter amount of coupons to generate.'),
            'Generate amount violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then I should be notified that generate code length is required
     */
    public function iShouldBeNotifiedThatCodeLengthIsRequired()
    {
        Assert::true(
            $this->generatePage->checkCodeLengthValidation('Please enter coupon code length.'),
            'Generate code length violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then /^there should still be only one coupon with code "([^"]+)" related to (this promotion)$/
     */
    public function thereShouldStillBeOnlyOneCouponWithCodeRelatedTo($code, PromotionInterface $promotion)
    {
        $this->indexPage->open(['promotionId' => $promotion->getId()]);

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['code' => $code]),
            sprintf('There is no coupon with code %s.', $code)
        );
    }

    /**
     * @Then I should be notified that coupon usage limit must be at least one
     */
    public function iShouldBeNotifiedThatCouponUsageLimitMustBeAtLeast()
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor('usage_limit', 'Coupon usage limit must be at least 1.'),
            'Min usage limit violation message should appear on page, but it did not.'
        );
    }

    /**
     * @Then /^(this coupon) should no longer exist in the coupon registry$/
     */
    public function couponShouldNotExistInTheRegistry(CouponInterface $coupon)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['code' => $coupon->getCode()]),
            sprintf('Coupon with code %s should not exist.', $coupon->getCode())
        );
    }

    /**
     * @Then I should be notified that it has been successfully generated
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyGenerated()
    {
        $this->notificationChecker->checkNotification('Success Promotion coupons have been successfully generated.', NotificationType::success());
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        $this->notificationChecker->checkNotification(
            "Error Cannot delete, the promotion coupon is in use.",
            NotificationType::failure()
        );
    }

    /**
     * @Then /^(this coupon) should still exist in the registry$/
     */
    public function couponShouldStillExistInTheRegistry(CouponInterface $coupon)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['code' => $coupon->getCode()]),
            sprintf('Coupon with code %s should exist.', $coupon->getCode())
        );
    }

    /**
     * @Then /^I should be notified that generating (\d+) coupons with code length equal to (\d+) is not possible$/
     */
    public function iShouldBeNotifiedThatGeneratingCouponsWithCodeLengthIsNotPossible($amount, $codeLength)
    {
        $message = sprintf('Invalid coupons code length or coupons amount. It is not possible to generate %d unique coupons with code length equals %d. Possible generate amount is 8.', $amount, $codeLength);

        Assert::true(
            $this->generatePage->checkGenerationValidation($message),
            'Generate violation message should appear on page, but it does not.'
        );
    }
}
