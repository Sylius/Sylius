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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Element\Admin\PromotionCoupon\FormElementInterface;
use Sylius\Behat\Element\Admin\PromotionCoupon\GenerateFormElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Page\Admin\PromotionCoupon\GeneratePageInterface;
use Sylius\Behat\Page\Admin\PromotionCoupon\IndexPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Webmozart\Assert\Assert;

final class ManagingPromotionCouponsContext implements Context
{
    public function __construct(
        private readonly CreatePageInterface $createPage,
        private readonly GeneratePageInterface $generatePage,
        private readonly IndexPageInterface $indexPage,
        private readonly UpdatePageInterface $updatePage,
        private readonly FormElementInterface $formElement,
        private readonly GenerateFormElementInterface $generateFormElement,
        private readonly NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @Given /^I am browsing coupons of (this promotion)$/
     * @Given /^I browse coupons of (this promotion)$/
     * @When /^I want to view all coupons of (this promotion)$/
     * @When /^I browse all coupons of ("[^"]+" promotion)$/
     */
    public function iWantToViewAllCouponsOfThisPromotion(PromotionInterface $promotion): void
    {
        $this->indexPage->open(['promotionId' => $promotion->getId()]);
    }

    /**
     * @When /^I want to create a new coupon for (this promotion)$/
     */
    public function iWantToCreateANewCouponForThisPromotion(PromotionInterface $promotion): void
    {
        $this->createPage->open(['promotionId' => $promotion->getId()]);
    }

    /**
     * @When /^I want to modify the ("[^"]+" coupon) for (this promotion)$/
     */
    public function iWantToModifyTheCoupon(PromotionCouponInterface $coupon, PromotionInterface $promotion): void
    {
        $this->updatePage->open(['id' => $coupon->getId(), 'promotionId' => $promotion->getId()]);
    }

    /**
     * @When /^I want to generate new coupons for (this promotion)$/
     */
    public function iWantToGenerateNewCouponsForThisPromotion(PromotionInterface $promotion): void
    {
        $this->generatePage->open(['promotionId' => $promotion->getId()]);
    }

    /**
     * @When /^I specify their code length as (\d+)$/
     * @When I do not specify their code length
     */
    public function iSpecifyTheirCodeLengthAs(?int $codeLength = null): void
    {
        $this->generateFormElement->specifyCodeLength($codeLength);
    }

    /**
     * @When I specify their prefix as :prefix
     */
    public function specifyPrefixAs(string $prefix): void
    {
        $this->generateFormElement->specifyPrefix($prefix);
    }

    /**
     * @When I specify their suffix as :suffix
     */
    public function specifySuffixAs(string $suffix): void
    {
        $this->generateFormElement->specifySuffix($suffix);
    }

    /**
     * @When /^I limit generated coupons usage to (\d+) times?$/
     */
    public function iSetGeneratedCouponsUsageLimitTo(int $limit): void
    {
        $this->generateFormElement->setUsageLimit($limit);
    }

    /**
     * @When I make generated coupons valid until :date
     */
    public function iMakeGeneratedCouponsValidUntil(\DateTimeInterface $date): void
    {
        $this->generateFormElement->setExpiresAt($date);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        $this->formElement->specifyCode($code ?? '');
    }

    /**
     * @When I specify a too long code
     */
    public function iSpecifyATooLong(): void
    {
        $this->formElement->specifyCode(str_repeat('a', 256));
    }

    /**
     * @When I limit its usage to :limit time(s)
     */
    public function iLimitItsUsageLimitTo(int $limit): void
    {
        $this->formElement->setUsageLimit($limit);
    }

    /**
     * @When I change its usage limit to :limit
     */
    public function iChangeItsUsageLimitTo(int $limit): void
    {
        $this->formElement->setUsageLimit($limit);
    }

    /**
     * @When I specify its amount as :amount
     * @When I do not specify its amount
     * @When I choose the amount of :amount coupons to be generated
     */
    public function iSpecifyItsAmountAs(?int $amount = null): void
    {
        $this->generateFormElement->specifyAmount($amount);
    }

    /**
     * @When /^I limit its per customer usage to ([^"]+) times?$/
     */
    public function iLimitItsPerCustomerUsageLimitTo(int $limit): void
    {
        $this->formElement->setCustomerUsageLimit($limit);
    }

    /**
     * @When I change its per customer usage limit to :limit
     */
    public function iChangeItsPerCustomerUsageLimitTo(int $limit): void
    {
        $this->formElement->setCustomerUsageLimit($limit);
    }

    /**
     * @When I make it not reusable from cancelled orders
     */
    public function iMakeItReusableFromCancelledOrders(): void
    {
        $this->formElement->toggleReusableFromCancelledOrders(false);
    }

    /**
     * @When I make it valid until :date
     */
    public function iMakeItValidUntil(\DateTimeInterface $date): void
    {
        $this->formElement->setExpiresAt($date);
    }

    /**
     * @When I change its expiration date to :date
     */
    public function iChangeItsExpirationDateTo(\DateTimeInterface $date): void
    {
        $this->formElement->setExpiresAt($date);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I generate it
     * @When I generate these coupons
     * @When I try to generate it
     * @When I try to generate these coupons
     */
    public function iGenerateIt(): void
    {
        $this->generatePage->generate();
    }

    /**
     * @When /^I delete ("[^"]+" coupon) related to (this promotion)$/
     * @When /^I try to delete ("[^"]+" coupon) related to (this promotion)$/
     */
    public function iDeleteCouponRelatedToThisPromotion(PromotionCouponInterface $coupon, PromotionInterface $promotion): void
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
     * @When /^I sort coupons by (ascending|descending) number of uses$/
     */
    public function iSortCouponsByNumberOfUses(string $order): void
    {
        $this->sortBy($order, 'used');
    }

    /**
     * @When /^I sort coupons by (ascending|descending) code$/
     */
    public function iSortCouponsByCode(string $order): void
    {
        $this->sortBy($order, 'code');
    }

    /**
     * @When /^I sort coupons by (ascending|descending) usage limit$/
     */
    public function iSortCouponsByUsageLimit(string $order): void
    {
        $this->sortBy($order, 'usageLimit');
    }

    /**
     * @When /^I sort coupons by (ascending|descending) usage limit per customer$/
     */
    public function iSortCouponsByPerCustomerUsageLimit(string $order): void
    {
        $this->sortBy($order, 'perCustomerUsageLimit');
    }

    /**
     * @When /^I sort coupons by (ascending|descending) expiration date$/
     */
    public function iSortCouponsByExpirationDate(string $order): void
    {
        $this->sortBy($order, 'expiresAt');
    }

    /**
     * @Then /^there should(?:| still) be (\d+) coupons? related to (this promotion)$/
     */
    public function thereShouldBeCouponRelatedTo(int $number, PromotionInterface $promotion): void
    {
        $this->indexPage->open(['promotionId' => $promotion->getId()]);

        Assert::same($this->indexPage->countItems(), $number);
    }

    /**
     * @When I filter by code containing :phrase
     */
    public function iFilterByCodeContaining(string $phrase): void
    {
        $this->indexPage->filterByCode($phrase);
        $this->indexPage->filter();
    }

    /**
     * @Then all of the coupon codes should be prefixed with :prefix
     */
    public function allOfTheCouponCodesShouldBePrefixedWith(string $prefix): void
    {
        foreach ($this->indexPage->getColumnFields('code') as $couponCode) {
            Assert::startsWith($couponCode, $prefix);
        }
    }

    /**
     * @Then all of the coupon codes should be suffixed with :suffix
     */
    public function allOfTheCouponCodesShouldBeSuffixedWith(string $suffix): void
    {
        foreach ($this->indexPage->getColumnFields('code') as $couponCode) {
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
     * @Then there should be a coupon with code :code
     * @Then there should be a :promotion promotion with a coupon code :code
     * @Then I should see the promotion coupon :code in the list
     */
    public function thereShouldBeCouponWithCode(string $code): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $code]));
    }

    /**
     * @Then this coupon should be valid until :date
     */
    public function thisCouponShouldBeValidUntil(\DateTime $date): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['expiresAt' => $date->format('d-m-Y')]));
    }

    /**
     * @Then /^this coupon should have (\d+) usage limit$/
     */
    public function thisCouponShouldHaveUsageLimit($limit): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['usageLimit' => $limit]));
    }

    /**
     * @Then /^("[^"]+" coupon) should be used (\d+) time(?:|s)$/
     */
    public function couponShouldHaveUsageLimit(PromotionCouponInterface $promotionCoupon, $used): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $promotionCoupon->getCode(), 'used' => $used]));
    }

    /**
     * @Then /^this coupon should have (\d+) per customer usage limit$/
     */
    public function thisCouponShouldHavePerCustomerUsageLimit($limit): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['perCustomerUsageLimit' => $limit]));
    }

    /**
     * @Then /^(this coupon) should not be reusable from cancelled orders$/
     */
    public function thisCouponShouldBeReusableFromCancelledOrders(PromotionCouponInterface $coupon): void
    {
        $this->updatePage->open(['id' => $coupon->getId(), 'promotionId' => $coupon->getPromotion()->getId()]);

        Assert::false($this->formElement->isReusableFromCancelledOrders());
    }

    /**
     * @Then I should not be able to edit its code
     * @Then the code field should be disabled
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->formElement->isCodeDisabled());
    }

    /**
     * @Then I should be notified that code is too long
     */
    public function iShouldBeNotifiedThatCodeIsTooLong(): void
    {
        Assert::contains(
            $this->formElement->getValidationMessage('code'),
            'must not be longer than 255 characters.',
        );
    }

    /**
     * @Then /^I should be notified that coupon with this code already exists$/
     */
    public function iShouldBeNotifiedThatCouponWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->formElement->getValidationMessage('code'), 'This coupon already exists.');
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element): void
    {
        Assert::same($this->formElement->getValidationMessage($element), sprintf('Please enter coupon %s.', $element));
    }

    /**
     * @Then I should be notified that generate amount is required
     */
    public function iShouldBeNotifiedThatGenerateAmountIsRequired(): void
    {
        Assert::same($this->generateFormElement->getValidationMessage('amount'), 'Please enter amount of coupons to generate.');
    }

    /**
     * @Then I should be notified that generate code length is required
     */
    public function iShouldBeNotifiedThatCodeLengthIsRequired(): void
    {
        Assert::same($this->generateFormElement->getValidationMessage('code_length'), 'Please enter coupon code length.');
    }

    /**
     * @Then I should be notified that generate code length is out of range
     */
    public function iShouldBeNotifiedThatCodeLengthIsOutOfRange(): void
    {
        Assert::same($this->generateFormElement->getValidationMessage('code_length'), 'Coupon code length must be between 1 and 40.');
    }

    /**
     * @Then /^there should still be only one coupon with code "([^"]+)" related to (this promotion)$/
     */
    public function thereShouldStillBeOnlyOneCouponWithCodeRelatedTo($code, PromotionInterface $promotion): void
    {
        $this->indexPage->open(['promotionId' => $promotion->getId()]);

        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $code]));
    }

    /**
     * @Then I should be notified that coupon usage limit must be at least one
     */
    public function iShouldBeNotifiedThatCouponUsageLimitMustBeAtLeast(): void
    {
        Assert::same($this->formElement->getValidationMessage('usage_limit'), 'Coupon usage limit must be at least 1.');
    }

    /**
     * @Then I should be notified that coupon usage limit per customer must be at least one
     */
    public function iShouldBeNotifiedThatCouponUsageLimitPerCustomerMustBeAtLeast(): void
    {
        Assert::same($this->formElement->getValidationMessage('per_customer_usage_limit'), 'Coupon usage limit per customer must be at least 1.');
    }

    /**
     * @Then /^(this coupon) should no longer exist in the coupon registry$/
     */
    public function couponShouldNotExistInTheRegistry(PromotionCouponInterface $coupon): void
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
    public function iShouldBeNotifiedOfFailure(): void
    {
        $this->notificationChecker->checkNotification(
            'Error Cannot delete, the Promotion coupon is in use.',
            NotificationType::error(),
        );
    }

    /**
     * @Then /^(this coupon) should still exist in the registry$/
     */
    public function couponShouldStillExistInTheRegistry(PromotionCouponInterface $coupon): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $coupon->getCode()]));
    }

    /**
     * @Then I should be notified that generating :amount coupons with code length equal to :codeLength is not possible
     */
    public function iShouldBeNotifiedThatGeneratingCouponsWithCodeLengthIsNotPossible(int $amount, int $codeLength): void
    {
        Assert::contains(
            $this->generateFormElement->getFormValidationMessage(),
            sprintf('Invalid coupons code length or coupons amount. It is not possible to generate %d unique coupons with code length %d.', $amount, $codeLength)
        );
    }

    /**
     * @Then I should see the coupon :couponCode in the list
     */
    public function iShouldSeeTheCouponInTheList(string $couponCode): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $couponCode]));
    }

    /**
     * @Then I should see :count coupons on the list
     */
    public function iShouldSeeCountCouponsOnTheList(int $count): void
    {
        Assert::same($this->indexPage->countItems(), $count);
    }

    /**
     * @Then the first coupon should have code :code
     */
    public function theFirstCouponShouldHaveCode(string $code): void
    {
        Assert::same($this->indexPage->getColumnFields('code')[0], $code);
    }

    /**
     * @Then I should see a single promotion coupon in the list
     * @Then there should be :amount promotion coupons
     */
    public function thereShouldBePromotionCoupon(int $amount = 1): void
    {
        Assert::same($this->indexPage->countItems(), $amount);
    }

    private function sortBy(string $order, string $field): void
    {
        $this->indexPage->sortBy($field, str_starts_with($order, 'de') ? 'desc' : 'asc');
    }
}
