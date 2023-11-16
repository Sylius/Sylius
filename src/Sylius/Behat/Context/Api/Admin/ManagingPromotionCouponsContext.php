<?php

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Converter\SectionAwareIriConverterInterface;
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
        Assert::true($this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'code', $code));
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

    private function sortBy(string $order, string $field): void
    {
        $this->client->sort([$field => str_starts_with($order, 'de') ? 'desc' : 'asc']);
    }
}
