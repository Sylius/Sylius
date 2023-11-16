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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Converter\SectionAwareIriConverterInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Webmozart\Assert\Assert;

final class ManagingPromotionCouponsContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
    ) {
    }

    /**
     * @Given /^I am browsing coupons of (this promotion)$/
     * @When /^I want to view all coupons of (this promotion)$/
     */
    public function iWantToViewAllCouponsOfThisPromotion(PromotionInterface $promotion): void
    {
        $this->client->index(Resources::PROMOTION_COUPONS);
        $this->client->addFilter(
            'promotion',
            $this->sectionAwareIriConverter->getIriFromResourceInSection($promotion, 'admin'),
        );
        $this->client->filter();
    }

    /**
     * @When /^I want to create a new coupon for (this promotion)$/
     */
    public function iWantToCreateANewCouponForPromotion(PromotionInterface $promotion): void
    {
        $this->client->buildCreateRequest(Resources::PROMOTION_COUPONS);
        $this->client->addRequestData(
            'promotion',
            $this->sectionAwareIriConverter->getIriFromResourceInSection($promotion, 'admin'),
        );
    }

    /**
     * @When /^I delete ("[^"]+" coupon) related to this promotion$/
     * @When /^I try to delete ("[^"]+" coupon) related to this promotion$/
     */
    public function iDeleteCouponRelatedToThisPromotion(PromotionCouponInterface $coupon): void
    {
        $this->client->delete(Resources::PROMOTION_COUPONS, $coupon->getCode());
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I limit its usage to :times times
     */
    public function iLimitItsUsageToTimes(int $times): void
    {
        $this->client->addRequestData('usageLimit', $times);
    }

    /**
     * @When I limit its per customer usage to :times times
     */
    public function iLimitItsPerCustomerUsageToTimes(int $times): void
    {
        $this->client->addRequestData('perCustomerUsageLimit', $times);
    }

    /**
     * @When I make it valid until :date
     */
    public function iMakeItValidUntil(\DateTime $date): void
    {
        $this->client->addRequestData('expiresAt', $date->format('d-m-Y'));
    }

    /**
     * @When I add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
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
    public function thereShouldBeCountCouponsRelatedToThisPromotion(int $count, PromotionInterface $promotion): void
    {
        $coupons = $this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'promotion',
            $this->sectionAwareIriConverter->getIriFromResourceInSection($promotion, 'admin'),
        );

        Assert::same(count($coupons), $count);
    }

    /**
     * @Then there should be a coupon with code :code
     */
    public function thereShouldBeACouponWithCode(string $code): void
    {
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->index(Resources::PROMOTION_COUPONS),
            'code',
            $code,
        ));
    }

    /**
     * @Then I should see :count coupons on the list
     */
    public function iShouldSeeCountCouponsOnTheList(int $count): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then the first coupon should have code :code
     */
    public function theFirstCouponShouldHaveCode(string $code): void
    {
        Assert::true($this->responseChecker->hasItemOnPositionWithValue(
            $this->client->getLastResponse(),
            0,
            'code',
            $code,
        ));
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Promotion coupon could not be created',
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Promotion coupon could not be deleted',
        );
    }

    /**
     * @Then /^(this coupon) should no longer exist in the coupon registry$/
     */
    public function couponShouldNotExistInTheRegistry(PromotionCouponInterface $coupon): void
    {
        Assert::false($this->responseChecker->hasItemWithValue(
            $this->client->index(Resources::PROMOTION_COUPONS),
            'code',
            $coupon->getCode(),
        ));
    }

    /**
     * @Then /^(this coupon) should still exist in the registry$/
     */
    public function couponShouldStillExistInTheRegistry(PromotionCouponInterface $coupon): void
    {
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->index(Resources::PROMOTION_COUPONS),
            'code',
            $coupon->getCode(),
        ));
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedThatItIsInUseAndCannotBeDeleted(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot delete, the promotion coupon is in use.',
        );
    }

    private function sortBy(string $order, string $field): void
    {
        $this->client->sort([$field => str_starts_with($order, 'de') ? 'desc' : 'asc']);
    }
}
