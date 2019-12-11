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
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\PromotionCoupon\CreatePageInterface;
use Sylius\Behat\Page\Admin\PromotionCoupon\GeneratePageInterface;
use Sylius\Behat\Page\Admin\PromotionCoupon\IndexPageInterface;
use Sylius\Behat\Page\Admin\PromotionCoupon\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Webmozart\Assert\Assert;

final class ManagingPromotionCouponsContext implements Context
{
    /** @var CreatePageInterface */
    private $createPage;

    /** @var GeneratePageInterface */
    private $generatePage;

    /** @var IndexPageInterface */
    private $indexPage;

    /** @var UpdatePageInterface */
    private $updatePage;

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

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
     * @Given /^I browse coupons of (this promotion)$/
     * @Given /^I want to view all coupons of (this promotion)$/
     * @When /^I browse all coupons of ("[^"]+" promotion)$/
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
    public function iWantToModifyTheCoupon(PromotionCouponInterface $coupon, PromotionInterface $promotion)
    {
        $this->updatePage->open(['id' => $coupon->getId(), 'promotionId' => $promotion->getId()]);
    }

    /**
     * @When /^I want to generate new coupons for (this promotion)$/
     */
    public function iWantToGenerateNewCouponsForThisPromotion(PromotionInterface $promotion)
    {
        $this->generatePage->open(['promotionId' => $promotion->getId()]);
    }

    /**
     * @When /^I specify their code length as (\d+)$/
     * @When I do not specify their code length
     */
    public function iSpecifyTheirCodeLengthAs(?int $codeLength = null): void
    {
        $this->generatePage->specifyCodeLength($codeLength);
    }

    /**
     * @When I specify their prefix as :prefix
     */
    public function specifyPrefixAs(string $prefix): void
    {
        $this->generatePage->specifyPrefix($prefix);
    }

    /**
     * @When I specify their suffix as :suffix
     */
    public function specifySuffixAs(string $suffix): void
    {
        $this->generatePage->specifySuffix($suffix);
    }

    /**
     * @When /^I limit generated coupons usage to (\d+) times$/
     */
    public function iSetGeneratedCouponsUsageLimitTo(int $limit)
    {
        $this->generatePage->setUsageLimit($limit);
    }

    /**
     * @When I make generated coupons valid until :date
     */
    public function iMakeGeneratedCouponsValidUntil(\DateTimeInterface $date)
    {
        $this->generatePage->setExpiresAt($date);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code ?? '');
    }

    /**
     * @When I limit its usage to :limit times
     */
    public function iLimitItsUsageLimitTo(int $limit)
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
     * @When I choose the amount of :amount coupons to be generated
     */
    public function iSpecifyItsAmountAs(?int $amount = null): void
    {
        $this->generatePage->specifyAmount($amount);
    }

    /**
     * @When I limit its per customer usage to :limit times
     */
    public function iLimitItsPerCustomerUsageLimitTo(int $limit)
    {
        $this->createPage->setCustomerUsageLimit($limit);
    }

    /**
     * @When I change its per customer usage limit to :limit
     */
    public function iChangeItsPerCustomerUsageLimitTo(int $limit)
    {
        $this->updatePage->setCustomerUsageLimit($limit);
    }

    /**
     * @When I make it valid until :date
     */
    public function iMakeItValidUntil(\DateTimeInterface $date)
    {
        $this->createPage->setExpiresAt($date);
    }

    /**
     * @When I change expires date to :date
     */
    public function iChangeExpiresDateTo(\DateTimeInterface $date)
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
     * @When I generate these coupons
     * @When I try to generate it
     * @When I try to generate these coupons
     */
    public function iGenerateIt()
    {
        $this->generatePage->generate();
    }

    /**
     * @When /^I delete ("[^"]+" coupon) related to (this promotion)$/
     * @When /^I try to delete ("[^"]+" coupon) related to (this promotion)$/
     */
    public function iDeleteCouponRelatedToThisPromotion(PromotionCouponInterface $coupon, PromotionInterface $promotion)
    {
        $this->indexPage->open(['promotionId' => $promotion->getId()]);
        $this->indexPage->deleteResourceOnPage(['code' => $coupon->getCode()]);
    }

    /**
     * @When I check (also) the :couponCode coupon
     */
    public function iCheckTheCoupon(string $couponCode): void
    {
        $this->indexPage->checkResourceOnPage(['code' => $couponCode]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then /^there should be (0|1) coupon related to (this promotion)$/
     * @Then /^there should be (\b(?![01]\b)\d{1,9}\b) coupons related to (this promotion)$/
     * @Then /^there should still be (\d+) coupons related to (this promotion)$/
     */
    public function thereShouldBeCouponRelatedTo(int $number, PromotionInterface $promotion): void
    {
        $this->indexPage->open(['promotionId' => $promotion->getId()]);

        Assert::same($this->indexPage->countItems(), $number);
    }

    /**
     * @Then all of the coupon codes should be prefixed with :prefix
     */
    public function allOfTheCouponCodesShouldBePrefixedWith(string $prefix): void
    {
        foreach ($this->indexPage->getCouponCodes() as $couponCode) {
            Assert::startsWith($couponCode, $prefix);
        }
    }

    /**
     * @Then all of the coupon codes should be suffixed with :suffix
     */
    public function allOfTheCouponCodesShouldBeSuffixedWith(string $suffix): void
    {
        foreach ($this->indexPage->getCouponCodes() as $couponCode) {
            Assert::endsWith($couponCode, $suffix);
        }
    }

    /**
     * @Then I should see a single coupon in the list
     */
    public function iShouldSeeASingleCouponInTheList(): void
    {
        Assert::same($this->indexPage->countItems(), 1);
    }

    /**
     * @Then /^there should be coupon with code "([^"]+)"$/
     */
    public function thereShouldBeCouponWithCode($code)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $code]));
    }

    /**
     * @Then this coupon should be valid until :date
     */
    public function thisCouponShouldBeValidUntil(\DateTimeInterface $date)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['expiresAt' => date('d-m-Y', $date->getTimestamp())]));
    }

    /**
     * @Then /^this coupon should have (\d+) usage limit$/
     */
    public function thisCouponShouldHaveUsageLimit($limit)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['usageLimit' => $limit]));
    }

    /**
     * @Then /^("[^"]+" coupon) should be used (\d+) time(?:|s)$/
     */
    public function couponShouldHaveUsageLimit(PromotionCouponInterface $promotionCoupon, $used)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $promotionCoupon->getCode(), 'used' => $used]));
    }

    /**
     * @Then /^this coupon should have (\d+) per customer usage limit$/
     */
    public function thisCouponShouldHavePerCustomerUsageLimit($limit)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['perCustomerUsageLimit' => $limit]));
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then /^I should be notified that coupon with this code already exists$/
     */
    public function iShouldBeNotifiedThatCouponWithThisCodeAlreadyExists()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('code'), 'This coupon already exists.');
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), sprintf('Please enter coupon %s.', $element));
    }

    /**
     * @Then I should be notified that generate amount is required
     */
    public function iShouldBeNotifiedThatGenerateAmountIsRequired()
    {
        Assert::true($this->generatePage->checkAmountValidation('Please enter amount of coupons to generate.'));
    }

    /**
     * @Then I should be notified that generate code length is required
     */
    public function iShouldBeNotifiedThatCodeLengthIsRequired()
    {
        Assert::true($this->generatePage->checkCodeLengthValidation('Please enter coupon code length.'));
    }

    /**
     * @Then /^there should still be only one coupon with code "([^"]+)" related to (this promotion)$/
     */
    public function thereShouldStillBeOnlyOneCouponWithCodeRelatedTo($code, PromotionInterface $promotion)
    {
        $this->indexPage->open(['promotionId' => $promotion->getId()]);

        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $code]));
    }

    /**
     * @Then I should be notified that coupon usage limit must be at least one
     */
    public function iShouldBeNotifiedThatCouponUsageLimitMustBeAtLeast()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('usage_limit'), 'Coupon usage limit must be at least 1.');
    }

    /**
     * @Then /^(this coupon) should no longer exist in the coupon registry$/
     */
    public function couponShouldNotExistInTheRegistry(PromotionCouponInterface $coupon)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $coupon->getCode()]));
    }

    /**
     * @Then I should be notified that they have been successfully generated
     */
    public function iShouldBeNotifiedThatTheyHaveBeenSuccessfullyGenerated(): void
    {
        $this->notificationChecker->checkNotification('Success Promotion coupons have been successfully generated.', NotificationType::success());
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        $this->notificationChecker->checkNotification(
            'Error Cannot delete, the promotion coupon is in use.',
            NotificationType::failure()
        );
    }

    /**
     * @Then /^(this coupon) should still exist in the registry$/
     */
    public function couponShouldStillExistInTheRegistry(PromotionCouponInterface $coupon)
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $coupon->getCode()]));
    }

    /**
     * @Then I should be notified that generating :amount coupons with code length equal to :codeLength is not possible
     */
    public function iShouldBeNotifiedThatGeneratingCouponsWithCodeLengthIsNotPossible(int $amount, int $codeLength): void
    {
        Assert::true($this->generatePage->checkGenerationValidation(sprintf(
            'Invalid coupons code length or coupons amount. It is not possible to generate %d unique coupons with code length %d.',
            $amount,
            $codeLength
        )));
    }

    /**
     * @Then I should see the coupon :couponCode in the list
     */
    public function iShouldSeeTheCouponInTheList(string $couponCode): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $couponCode]));
    }
}
