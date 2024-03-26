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
use Sylius\Behat\Client\RequestFactoryInterface;
use Sylius\Behat\Client\RequestInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Admin\Helper\ValidationTrait;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Context\Api\Subresources;
use Sylius\Behat\Service\Converter\SectionAwareIriConverterInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Webmozart\Assert\Assert;

final class ManagingPromotionCouponsContext implements Context
{
    use ValidationTrait;

    private ?RequestInterface $request = null;

    public function __construct(
        private ApiClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private ResponseCheckerInterface $responseChecker,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
    ) {
    }

    /**
     * @Given /^I am browsing coupons of (this promotion)$/
     * @When /^I want to view all coupons of (this promotion)$/
     * @When /^I browse all coupons of ("[^"]+" promotion)$/
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
     * @When I want to create a new coupon
     */
    public function iWantToCreateANewCoupon(): void
    {
        $this->client->buildCreateRequest(Resources::PROMOTION_COUPONS);
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
     * @When I want to modify the :coupon coupon for this promotion
     */
    public function iWantToModifyTheCouponOfThisPromotion(PromotionCouponInterface $coupon): void
    {
        $this->client->buildUpdateRequest(Resources::PROMOTION_COUPONS, $coupon->getCode());
    }

    /**
     * @When /^I want to generate new coupons for (this promotion)$/
     */
    public function iWantToGenerateNewCouponsForThisPromotion(PromotionInterface $promotion): void
    {
        $this->request = $this->requestFactory->create('admin', 'promotion-coupons/generate', 'Bearer');
        $this->request->updateContent(['promotionCode' => $promotion->getCode()]);
    }

    /**
     * @When I (try to) delete :coupon coupon related to this promotion
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
     * @When I limit its usage to :times time(s)
     * @When I change its usage limit to :times
     */
    public function iLimitItsUsageToTimes(int $times): void
    {
        $this->client->addRequestData('usageLimit', $times);
    }

    /**
     * @When I limit its per customer usage to :times time(s)
     * @When I change its per customer usage limit to :times
     */
    public function iLimitItsPerCustomerUsageToTimes(int $times): void
    {
        $this->client->addRequestData('perCustomerUsageLimit', $times);
    }

    /**
     * @When I make it valid until :date
     * @When I change its expiration date to :date
     */
    public function iMakeItValidUntil(\DateTime $date): void
    {
        $this->client->addRequestData('expiresAt', $date->format('d-m-Y'));
    }

    /**
     * @When I make it not reusable from cancelled orders
     */
    public function iMakeItNotReusableFromCancelledOrders(): void
    {
        $this->client->addRequestData('reusableFromCancelledOrders', false);
    }

    /**
     * @When I choose the amount of :amount coupons to be generated
     */
    public function iSpecifyItsAmountAs(int $amount): void
    {
        $this->request->updateContent(['amount' => $amount]);
    }

    /**
     * @When I specify their prefix as :prefix
     */
    public function iSpecifyPrefixAs(string $prefix): void
    {
        $this->request->updateContent(['prefix' => $prefix]);
    }

    /**
     * @When I specify their suffix as :suffix
     */
    public function iSpecifySuffixAs(string $suffix): void
    {
        $this->request->updateContent(['suffix' => $suffix]);
    }

    /**
     * @When /^I specify their code length as (\d+)$/
     * @When I do not specify their code length
     */
    public function iSpecifyTheirCodeLengthAs(?int $codeLength = null): void
    {
        $this->request->updateContent(['codeLength' => $codeLength]);
    }

    /**
     * @When /^I limit generated coupons usage to (\d+) times?$/
     */
    public function iSetGeneratedCouponsUsageLimitTo(int $limit): void
    {
        $this->request->updateContent(['usageLimit' => $limit]);
    }

    /**
     * @When I make generated coupons valid until :date
     */
    public function iMakeGeneratedCouponsValidUntil(\DateTimeInterface $date): void
    {
        $this->request->updateContent(['expiresAt' => $date->format('Y-m-d')]);
    }

    /**
     * @When I do not specify its :field
     */
    public function iDoNotSpecifyIts(): void
    {
        // Intentionally left blank
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I (try to) generate these coupons
     */
    public function iGenerateTheseCoupons(): void
    {
        $this->client->executeCustomRequest($this->request);
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
        $coupons = $this->responseChecker->getCollection(
            $this->client->subResourceIndex(Resources::PROMOTIONS, Subresources::PROMOTION_COUPONS, $promotion->getCode()),
        );
        Assert::same(count($coupons), $count);
    }

    /**
     * @Then there should be a :promotion promotion with a coupon code :code
     */
    public function thereShouldBeACouponWithCode(PromotionInterface $promotion, string $code): void
    {
        Assert::true($this->responseChecker->hasItemWithValue(
            $this->client->subResourceIndex(Resources::PROMOTIONS, Subresources::PROMOTION_COUPONS, $promotion->getCode()),
            'code',
            $code,
        ));
    }

    /**
     * @Then there should be no coupon with code :code
     */
    public function thereShouldBeNoCouponWithCode(string $code): void
    {
        Assert::false($this->responseChecker->hasItemWithValue(
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
     * @Then this coupon should be valid until :date
     */
    public function thisCouponShouldBeValidUntil(\DateTime $date): void
    {
        $actualDate = \DateTime::createFromFormat(
            'Y-m-d h:i:s',
            $this->responseChecker->getValue($this->client->getLastResponse(), 'expiresAt'),
        );

        Assert::same(
            $actualDate->format('Y-m-d'),
            $date->format('Y-m-d'),
        );
    }

    /**
     * @Then this coupon should have :limit usage limit
     */
    public function thisCouponShouldHaveUsageLimit(int $limit): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'usageLimit'),
            $limit,
        );
    }

    /**
     * @Then this coupon should have :limit per customer usage limit
     */
    public function thisCouponShouldHavePerCustomerUsageLimit(int $limit): void
    {
        Assert::same(
            $this->responseChecker->getValue($this->client->getLastResponse(), 'perCustomerUsageLimit'),
            $limit,
        );
    }

    /**
     * @Then this coupon should not be reusable from cancelled orders
     */
    public function thisCouponShouldNotBeReusableFromCancelledOrders(): void
    {
        Assert::false($this->responseChecker->getValue(
            $this->client->getLastResponse(),
            'reusableFromCancelledOrders',
        ));
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
     * @Then all of the coupon codes should be prefixed with :prefix
     */
    public function allOfTheCouponCodesShouldBePrefixedWith(string $prefix): void
    {
        foreach ($this->responseChecker->getCollection($this->client->getLastResponse()) as $promotionCoupon) {
            Assert::startsWith($promotionCoupon['code'], $prefix);
        }
    }

    /**
     * @Then all of the coupon codes should be suffixed with :suffix
     */
    public function allOfTheCouponCodesShouldBeSuffixedWith(string $suffix): void
    {
        foreach ($this->responseChecker->getCollection($this->client->getLastResponse()) as $promotionCoupon) {
            Assert::endsWith($promotionCoupon['code'], $suffix);
        }
    }

    /**
     * @Then /^there should still be only one coupon with code "([^"]+)" related to (this promotion)$/
     */
    public function thereShouldStillBeOnlyOneCouponWithCodeRelatedTo(string $code, PromotionInterface $promotion): void
    {
        $coupons = $this->responseChecker->getCollectionItemsWithValue(
            $this->client->subResourceIndex(Resources::PROMOTIONS, Subresources::PROMOTION_COUPONS, $promotion->getCode()),
            'code',
            $code,
        );

        Assert::count($coupons, 1);
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

    /**
     * @Then I should be notified that code is required
     */
    public function iShouldBeNotifiedThatCodeIsRequired(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Coupon has been created successfully, but it should not',
        );
        Assert::same($this->responseChecker->getError($response), 'code: Please enter coupon code.');
    }

    /**
     * @Then I should be notified that coupon usage limit must be at least one
     */
    public function iShouldBeNotifiedThatCouponUsageLimitMustBeAtLeastOne(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Coupon has been created successfully, but it should not',
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'usageLimit: Coupon usage limit must be at least 1.',
        );
    }

    /**
     * @Then I should be notified that coupon usage limit per customer must be at least one
     */
    public function iShouldBeNotifiedThatCouponUsageLimitPerCustomerMustBeAtLeastOne(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Coupon has been created successfully, but it should not',
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'perCustomerUsageLimit: Coupon usage limit per customer must be at least 1.',
        );
    }

    /**
     * @Then I should be notified that promotion is required
     */
    public function iShouldBeNotifiedThatPromotionIsRequired(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Coupon has been created successfully, but it should not',
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'promotion: Please provide a promotion for this coupon.',
        );
    }

    /**
     * @Then I should be notified that only coupon based promotions can have coupons
     */
    public function iShouldBeNotifiedThatOnlyCouponBasedPromotionsCanHaveCoupons(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Coupon has been created successfully, but it should not',
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'promotion: Only coupon based promotions can have coupons.',
        );
    }

    /**
     * @Then I should be notified that generating :amount coupons with code length equal to :codeLength is not possible
     */
    public function iShouldBeNotifiedThatGeneratingCouponsWithCodeLengthIsNotPossible(int $amount, int $codeLength): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf(
                'Invalid coupon code length or coupons amount. It is not possible to generate %d unique coupons with %d code length',
                $amount,
                $codeLength,
            ),
        );
    }

    /**
     * @Then I should be notified that generate amount is required
     */
    public function iShouldBeNotifiedThatGenerateAmountIsRequired(): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'amount: Please enter amount of coupons to generate.',
        );
    }

    /**
     * @Then I should be notified that generate code length is required
     */
    public function iShouldBeNotifiedThatCodeLengthIsRequired(): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'codeLength: Please enter coupon code length.',
        );
    }

    /**
     * @Then I should be notified that generate code length is out of range
     */
    public function iShouldBeNotifiedThatCodeLengthIsOutOfRange(): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'codeLength: Coupon code length must be between 1 and 40.',
        );
    }

    /**
     * @Then I should be notified that they have been successfully generated
     */
    public function iShouldBeNotifiedThatTheyHaveBeenSuccessfullyGenerated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Promotion coupon could not be generated',
        );
    }

    /**
     * @Then I should be notified that coupon with this code already exists
     */
    public function iShouldBeNotifiedThatCouponWithThisCodeAlreadyExists(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Coupon has been created successfully, but it should not',
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'code: This coupon already exists.',
        );
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);

        Assert::false($this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'));
    }

    /**
     * @Then /^("[^"]+" coupon) should be used (\d+) time(?:|s)$/
     */
    public function couponShouldHaveUsageLimit(PromotionCouponInterface $promotionCoupon, int $used): void
    {
        $returnedPromotionCoupon = current($this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'code',
            $promotionCoupon->getCode(),
        ));

        Assert::same(
            $returnedPromotionCoupon['used'],
            $used,
            sprintf('The promotion coupon %s has been used %s times', $promotionCoupon->getCode(), $returnedPromotionCoupon['used']),
        );
    }

    private function sortBy(string $order, string $field): void
    {
        $this->client->sort([$field => str_starts_with($order, 'de') ? 'desc' : 'asc']);
    }
}
