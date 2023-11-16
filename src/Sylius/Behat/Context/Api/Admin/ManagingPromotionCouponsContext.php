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
}
