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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Factory\PromotionActionFactoryInterface;
use Sylius\Component\Core\Factory\PromotionRuleFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\CustomerGroupRuleChecker;
use Sylius\Component\Core\Test\Factory\TestPromotionFactoryInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Promotion\Factory\PromotionCouponFactoryInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

final class PromotionContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private PromotionActionFactoryInterface $actionFactory,
        private PromotionCouponFactoryInterface $couponFactory,
        private PromotionRuleFactoryInterface $ruleFactory,
        private TestPromotionFactoryInterface $testPromotionFactory,
        private PromotionRepositoryInterface $promotionRepository,
        private PromotionCouponGeneratorInterface $couponGenerator,
        private ObjectManager $objectManager,
    ) {
    }

    /**
     * @Given there is (also) a promotion :name
     * @Given there is a promotion :name that applies to discounted products
     * @Given there is a promotion :name identified by :code code
     */
    public function thereIsPromotion(string $name, ?string $code = null): void
    {
        $this->createPromotion($name, $code);
    }

    /**
     * @Given /^there is a promotion "([^"]+)" with "Has at least one from taxons" rule (configured with "[^"]+" and "[^"]+")$/
     */
    public function thereIsAPromotionWithHasAtLeastOneFromTaxonsRuleConfiguredWith(string $name, array $taxons): void
    {
        $promotion = $this->createPromotion($name);
        $rule = $this->ruleFactory->createHasTaxon([$taxons[0]->getCode(), $taxons[1]->getCode()]);
        $promotion->addRule($rule);

        $this->objectManager->flush();
    }

    /**
     * @Given /^there is a promotion "([^"]+)" with "Total price of items from taxon" rule configured with ("[^"]+" taxon) and (?:€|£|\$)([^"]+) amount for ("[^"]+" channel)$/
     */
    public function thereIsAPromotionWithTotalPriceOfItemsFromTaxonRuleConfiguredWithTaxonAndAmountForChannel(
        string $name,
        TaxonInterface $taxon,
        int $amount,
        ChannelInterface $channel,
    ): void {
        $promotion = $this->createPromotion($name);
        $rule = $this->ruleFactory->createItemsFromTaxonTotal($channel->getCode(), $taxon->getCode(), $amount);
        $promotion->addRule($rule);

        $this->objectManager->flush();
    }

    /**
     * @Given /^there is a promotion "([^"]+)" with priority ([^"]+)$/
     */
    public function thereIsAPromotionWithPriority(string $promotionName, int $priority): void
    {
        $promotion = $this->testPromotionFactory
            ->createForChannel($promotionName, $this->sharedStorage->get('channel'))
        ;

        $promotion->setPriority($priority);

        $this->promotionRepository->add($promotion);
        $this->sharedStorage->set('promotion', $promotion);
    }

    /**
     * @Given /^there is an exclusive promotion "([^"]+)"(?:| with priority (\d+))$/
     */
    public function thereIsAnExclusivePromotionWithPriority(string $promotionName, int $priority = 0): void
    {
        $promotion = $this->testPromotionFactory
            ->createForChannel($promotionName, $this->sharedStorage->get('channel'))
        ;

        $promotion->setExclusive(true);
        $promotion->setPriority($priority);

        $this->promotionRepository->add($promotion);
        $this->sharedStorage->set('promotion', $promotion);
    }

    /**
     * @Given there is a promotion :promotionName limited to :usageLimit usages
     */
    public function thereIsPromotionLimitedToUsages(string $promotionName, int $usageLimit): void
    {
        $promotion = $this->testPromotionFactory
            ->createForChannel($promotionName, $this->sharedStorage->get('channel'))
        ;

        $promotion->setUsageLimit($usageLimit);

        $this->promotionRepository->add($promotion);
        $this->sharedStorage->set('promotion', $promotion);
    }

    /**
     * @Given the store has promotion :promotionName with coupon :couponCode
     * @Given the store has a promotion :promotionName with a coupon :couponCode that is limited to :usageLimit usages
     */
    public function thereIsPromotionWithCoupon(string $promotionName, string $couponCode, ?int $usageLimit = null): void
    {
        $coupon = $this->createCoupon($couponCode, $usageLimit);

        $promotion = $this->testPromotionFactory
            ->createForChannel($promotionName, $this->sharedStorage->get('channel'))
        ;
        $promotion->addCoupon($coupon);
        $promotion->setCouponBased(true);

        $this->promotionRepository->add($promotion);

        $this->sharedStorage->set('promotion', $promotion);
        $this->sharedStorage->set('coupon', $coupon);
    }

    /**
     * @Given there is a promotion :name that does not apply to discounted products
     */
    public function thereIsAPromotionThatDoesNotApplyToDiscountedProducts(string $name): void
    {
        $promotion = $this->createPromotion($name);
        $promotion->setAppliesToDiscounted(false);

        $this->promotionRepository->add($promotion);

        $this->sharedStorage->set('promotion', $promotion);
    }

    /**
     * @Given /^(this promotion) has "([^"]+)", "([^"]+)" and "([^"]+)" coupons/
     */
    public function thisPromotionHasCoupons(PromotionInterface $promotion, string ...$couponCodes): void
    {
        foreach ($couponCodes as $couponCode) {
            $coupon = $this->createCoupon($couponCode);
            $promotion->addCoupon($coupon);
        }

        $promotion->setCouponBased(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) does not apply on discounted products$/
     */
    public function thisPromotionDoesNotApplyOnDiscountedProducts(PromotionInterface $promotion): void
    {
        $promotion->setAppliesToDiscounted(false);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) has already expired$/
     */
    public function thisPromotionHasExpired(PromotionInterface $promotion): void
    {
        $promotion->setEndsAt(new \DateTime('1 day ago'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) expires tomorrow$/
     */
    public function thisPromotionExpiresTomorrow(PromotionInterface $promotion): void
    {
        $promotion->setEndsAt(new \DateTime('tomorrow'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) has started yesterday$/
     */
    public function thisPromotionHasStartedYesterday(PromotionInterface $promotion): void
    {
        $promotion->setStartsAt(new \DateTime('1 day ago'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) starts tomorrow$/
     */
    public function thisPromotionStartsTomorrow(PromotionInterface $promotion): void
    {
        $promotion->setStartsAt(new \DateTime('tomorrow'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this coupon) has already expired$/
     */
    public function thisCouponHasExpired(PromotionCouponInterface $coupon): void
    {
        $coupon->setExpiresAt(new \DateTime('1 day ago'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this coupon) expires tomorrow$/
     */
    public function thisCouponExpiresTomorrow(PromotionCouponInterface $coupon): void
    {
        $coupon->setExpiresAt(new \DateTime('tomorrow'));

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this coupon) is set as non reusable after cancelling the order in which it has been used$/
     */
    public function thisIsSetAsNonReusableAfterCancellingTheOrderInWhichItHasBeenUsed(PromotionCouponInterface $coupon): void
    {
        $coupon->setReusableFromCancelledOrders(false);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this coupon) has already reached its usage limit$/
     */
    public function thisCouponHasReachedItsUsageLimit(PromotionCouponInterface $coupon): void
    {
        $coupon->setUsed(42);
        $coupon->setUsageLimit(42);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this coupon) can be used (\d+) times?$/
     * @Given /^(this coupon) can be used once$/
     */
    public function thisCouponCanBeUsedNTimes(PromotionCouponInterface $coupon, int $usageLimit = 1): void
    {
        $coupon->setUsageLimit($usageLimit);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this coupon) can be used once per customer$/
     */
    public function thisCouponCanBeUsedOncePerCustomer(PromotionCouponInterface $coupon): void
    {
        $coupon->setPerCustomerUsageLimit(1);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this coupon) can be used twice per customer$/
     */
    public function thisCouponCanBeUsedTwicePerCustomer(PromotionCouponInterface $coupon): void
    {
        $coupon->setPerCustomerUsageLimit(2);

        $this->objectManager->flush();
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") discount to every order$/
     */
    public function itGivesFixedDiscountToEveryOrder(PromotionInterface $promotion, int $discount): void
    {
        $this->createFixedPromotion($promotion, $discount);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") discount to every order in the ("[^"]+" channel) and ("(?:€|£|\$)[^"]+") discount to every order in the ("[^"]+" channel)$/
     */
    public function thisPromotionGivesDiscountToEveryOrderInTheChannelAndDiscountToEveryOrderInTheChannel(
        PromotionInterface $promotion,
        int $firstChannelDiscount,
        ChannelInterface $firstChannel,
        int $secondChannelDiscount,
        ChannelInterface $secondChannel,
    ): void {
        /** @var PromotionActionInterface $action */
        $action = $this->actionFactory->createFixedDiscount($firstChannelDiscount, $firstChannel->getCode());
        $action->setConfiguration(array_merge($action->getConfiguration(), [$secondChannel->getCode() => ['amount' => $secondChannelDiscount]]));

        $promotion->addChannel($firstChannel);
        $promotion->addChannel($secondChannel);
        $promotion->addAction($action);

        $this->objectManager->flush();
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") discount to every order$/
     */
    public function itGivesPercentageDiscountToEveryOrder(PromotionInterface $promotion, float $discount): void
    {
        $this->createPercentagePromotion($promotion, $discount);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") discount to every order with quantity at least ([^"]+)$/
     */
    public function itGivesFixedDiscountToEveryOrderWithQuantityAtLeast(
        PromotionInterface $promotion,
        int $discount,
        int $quantity,
    ): void {
        $rule = $this->ruleFactory->createCartQuantity($quantity);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") discount to every order with items total at least ("[^"]+")$/
     */
    public function itGivesFixedDiscountToEveryOrderWithItemsTotalAtLeast(
        PromotionInterface $promotion,
        int $discount,
        int $targetAmount,
    ): void {
        $channelCode = $this->getChannelCode();
        $rule = $this->ruleFactory->createItemTotal($channelCode, $targetAmount);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") discount to every order with items total at least ("[^"]+")$/
     */
    public function itGivesPercentageDiscountToEveryOrderWithItemsTotalAtLeast(
        PromotionInterface $promotion,
        float $discount,
        int $targetAmount,
    ): void {
        $channelCode = $this->getChannelCode();
        $rule = $this->ruleFactory->createItemTotal($channelCode, $targetAmount);

        $this->createPercentagePromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product when the item total is at least ("(?:€|£|\$)[^"]+")$/
     */
    public function itGivesOffOnEveryItemWhenItemTotalExceeds(
        PromotionInterface $promotion,
        float $discount,
        int $targetAmount,
    ): void {
        $channelCode = $this->getChannelCode();
        $rule = $this->ruleFactory->createItemTotal($channelCode, $targetAmount);

        $this->createUnitPercentagePromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") discount on shipping to every order$/
     */
    public function itGivesPercentageDiscountOnShippingToEveryOrder(PromotionInterface $promotion, float $discount): void
    {
        $action = $this->actionFactory->createShippingPercentageDiscount($discount);
        $promotion->addAction($action);

        $this->objectManager->flush();
    }

    /**
     * @Given /^([^"]+) gives free shipping to every order$/
     */
    public function thePromotionGivesFreeShippingToEveryOrder(PromotionInterface $promotion): void
    {
        $this->itGivesPercentageDiscountOnShippingToEveryOrder($promotion, 1);
    }

    /**
     * @Given /^([^"]+) gives(?:| another) ("[^"]+%") off every product (classified as "[^"]+")$/
     */
    public function itGivesPercentageOffEveryProductClassifiedAs(
        PromotionInterface $promotion,
        float $discount,
        TaxonInterface $taxon,
    ): void {
        $this->createUnitPercentagePromotion($promotion, $discount, $this->getTaxonFilterConfiguration([$taxon->getCode()]));
    }

    /**
     * @Given /^([^"]+) gives(?:| another) ("(?:€|£|\$)[^"]+") off on every product (classified as "[^"]+")$/
     */
    public function itGivesFixedOffEveryProductClassifiedAs(
        PromotionInterface $promotion,
        int $discount,
        TaxonInterface $taxon,
    ): void {
        $this->createUnitFixedPromotion($promotion, $discount, $this->getTaxonFilterConfiguration([$taxon->getCode()]));
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off on every product with minimum price at ("(?:€|£|\$)[^"]+")$/
     */
    public function thisPromotionGivesOffOnEveryProductWithMinimumPriceAt(
        PromotionInterface $promotion,
        int $discount,
        int $amount,
    ): void {
        $this->createUnitFixedPromotion($promotion, $discount, $this->getPriceRangeFilterConfiguration($amount));
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off on every product priced between ("(?:€|£|\$)[^"]+") and ("(?:€|£|\$)[^"]+")$/
     */
    public function thisPromotionGivesOffOnEveryProductPricedBetween(
        PromotionInterface $promotion,
        int $discount,
        int $minAmount,
        int $maxAmount,
    ): void {
        $this->createUnitFixedPromotion(
            $promotion,
            $discount,
            $this->getPriceRangeFilterConfiguration($minAmount, $maxAmount),
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product with minimum price at ("(?:€|£|\$)[^"]+")$/
     */
    public function thisPromotionPercentageGivesOffOnEveryProductWithMinimumPriceAt(
        PromotionInterface $promotion,
        float $discount,
        int $amount,
    ): void {
        $this->createUnitPercentagePromotion($promotion, $discount, $this->getPriceRangeFilterConfiguration($amount));
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product priced between ("(?:€|£|\$)[^"]+") and ("(?:€|£|\$)[^"]+")$/
     */
    public function thisPromotionPercentageGivesOffOnEveryProductPricedBetween(
        PromotionInterface $promotion,
        float $discount,
        int $minAmount,
        int $maxAmount,
    ): void {
        $this->createUnitPercentagePromotion(
            $promotion,
            $discount,
            $this->getPriceRangeFilterConfiguration($minAmount, $maxAmount),
        );
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off if order contains products (classified as "[^"]+")$/
     */
    public function thePromotionGivesOffIfOrderContainsProductsClassifiedAs(
        PromotionInterface $promotion,
        int $discount,
        TaxonInterface $taxon,
    ): void {
        $rule = $this->ruleFactory->createHasTaxon([$taxon->getCode()]);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off if order contains products (classified as "[^"]+" or "[^"]+")$/
     */
    public function thePromotionGivesOffIfOrderContainsProductsClassifiedAsOr(
        PromotionInterface $promotion,
        int $discount,
        array $taxons,
    ): void {
        $rule = $this->ruleFactory->createHasTaxon([$taxons[0]->getCode(), $taxons[1]->getCode()]);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off if order contains products (classified as "[^"]+") with a minimum value of ("(?:€|£|\$)[^"]+")$/
     */
    public function thePromotionGivesOffIfOrderContainsProductsClassifiedAsAndPricedAt(
        PromotionInterface $promotion,
        int $discount,
        TaxonInterface $taxon,
        int $amount,
    ): void {
        $channelCode = $this->getChannelCode();
        $rule = $this->ruleFactory->createItemsFromTaxonTotal($channelCode, $taxon->getCode(), $amount);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off customer's (\d)(?:st|nd|rd|th) order$/
     */
    public function itGivesFixedOffCustomersNthOrder(PromotionInterface $promotion, int $discount, int $nth): void
    {
        $rule = $this->ruleFactory->createNthOrder($nth);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on the customer's (\d)(?:st|nd|rd|th) order$/
     */
    public function itGivesPercentageOffCustomersNthOrder(PromotionInterface $promotion, float $discount, int $nth): void
    {
        $rule = $this->ruleFactory->createNthOrder($nth);

        $this->createPercentagePromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product (classified as "[^"]+") and ("(?:€|£|\$)[^"]+") discount on every order$/
     */
    public function itGivesPercentageOffOnEveryProductClassifiedAsAndAmountDiscountOnOrder(
        PromotionInterface $promotion,
        float $productDiscount,
        TaxonInterface $discountTaxon,
        int $orderDiscount,
    ): void {
        $this->createUnitPercentagePromotion($promotion, $productDiscount, $this->getTaxonFilterConfiguration([$discountTaxon->getCode()]));
        $this->createFixedPromotion($promotion, $orderDiscount);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off on every product classified as "[^"]+" and a free shipping to every order with items total equal at least ("[^"]+")$/
     */
    public function itGivesOffOnEveryProductClassifiedAsAndAFreeShippingToEveryOrderWithItemsTotalEqualAtLeast(
        PromotionInterface $promotion,
        int $discount,
        int $targetAmount,
    ): void {
        $freeShippingAction = $this->actionFactory->createShippingPercentageDiscount(1);
        $promotion->addAction($freeShippingAction);

        $channelCode = $this->getChannelCode();
        $rule = $this->ruleFactory->createItemTotal($channelCode, $targetAmount);

        $this->createUnitFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product (classified as "[^"]+") and a ("(?:€|£|\$)[^"]+") discount to every order with items total equal at least ("(?:€|£|\$)[^"]+")$/
     */
    public function itGivesOffOnEveryProductClassifiedAsAndAFixedDiscountToEveryOrderWithItemsTotalEqualAtLeast(
        PromotionInterface $promotion,
        float $taxonDiscount,
        TaxonInterface $taxon,
        int $orderDiscount,
        int $targetAmount,
    ): void {
        $channelCode = $this->getChannelCode();

        $orderDiscountAction = $this->actionFactory->createFixedDiscount($orderDiscount, $channelCode);
        $promotion->addAction($orderDiscountAction);

        $rule = $this->ruleFactory->createItemTotal($channelCode, $targetAmount);

        $this->createUnitPercentagePromotion(
            $promotion,
            $taxonDiscount,
            $this->getTaxonFilterConfiguration([$taxon->getCode()]),
            $rule,
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product (classified as "[^"]+" or "[^"]+") if order contains any product (classified as "[^"]+" or "[^"]+")$/
     */
    public function itGivesOffOnEveryProductClassifiedAsOrIfOrderContainsAnyProductClassifiedAsOr(
        PromotionInterface $promotion,
        float $discount,
        array $discountTaxons,
        array $targetTaxons,
    ): void {
        $discountTaxonsCodes = [$discountTaxons[0]->getCode(), $discountTaxons[1]->getCode()];
        $targetTaxonsCodes = [$targetTaxons[0]->getCode(), $targetTaxons[1]->getCode()];

        $rule = $this->ruleFactory->createHasTaxon($targetTaxonsCodes);

        $this->createUnitPercentagePromotion(
            $promotion,
            $discount,
            $this->getTaxonFilterConfiguration($discountTaxonsCodes),
            $rule,
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product (classified as "[^"]+") if order contains any product (classified as "[^"]+")$/
     */
    public function itGivesOffOnEveryProductClassifiedAsIfOrderContainsAnyProductClassifiedAs(
        PromotionInterface $promotion,
        float $discount,
        TaxonInterface $discountTaxon,
        TaxonInterface $targetTaxon,
    ): void {
        $rule = $this->ruleFactory->createHasTaxon([$targetTaxon->getCode()]);

        $this->createUnitPercentagePromotion(
            $promotion,
            $discount,
            $this->getTaxonFilterConfiguration([$discountTaxon->getCode()]),
            $rule,
        );
    }

    /**
     * @Given /^(it) is coupon based promotion$/
     * @Given /^(it) is a coupon based promotion$/
     */
    public function itIsCouponBasedPromotion(PromotionInterface $promotion): void
    {
        $promotion->setCouponBased(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(the promotion) was disabled for the (channel "[^"]+")$/
     */
    public function thePromotionWasDisabledForTheChannel(PromotionInterface $promotion, ChannelInterface $channel): void
    {
        $promotion->removeChannel($channel);

        $this->objectManager->flush();
    }

    /**
     * @Given /^the (coupon "[^"]+") was used up to its usage limit$/
     */
    public function theCouponWasUsed(PromotionCouponInterface $coupon): void
    {
        $coupon->setUsed($coupon->getUsageLimit());

        $this->objectManager->flush();
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off if order contains (?:a|an) ("[^"]+" product)$/
     */
    public function thePromotionGivesOffIfOrderContainsProducts(PromotionInterface $promotion, $discount, ProductInterface $product): void
    {
        $rule = $this->ruleFactory->createContainsProduct($product->getCode());

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off on a ("[^"]*" product)$/
     */
    public function itGivesFixedDiscountOffOnAProduct(PromotionInterface $promotion, $discount, ProductInterface $product): void
    {
        $this->createUnitFixedPromotion($promotion, $discount, $this->getProductsFilterConfiguration([$product->getCode()]));
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on a ("[^"]*" product)$/
     */
    public function itGivesPercentageDiscountOffOnAProduct(PromotionInterface $promotion, $discount, ProductInterface $product): void
    {
        $this->createUnitPercentagePromotion($promotion, $discount, $this->getProductsFilterConfiguration([$product->getCode()]));
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off the order for customers from ("[^"]*" group)$/
     */
    public function thePromotionGivesOffTheOrderForCustomersFromGroup(
        PromotionInterface $promotion,
        float $discount,
        CustomerGroupInterface $customerGroup,
    ): void {
        /** @var PromotionRuleInterface $rule */
        $rule = $this->ruleFactory->createNew();
        $rule->setType(CustomerGroupRuleChecker::TYPE);
        $rule->setConfiguration(['group_code' => $customerGroup->getCode()]);

        $this->createPercentagePromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") discount on shipping to every order over ("(?:€|£|\$)[^"]+")$/
     */
    public function itGivesDiscountOnShippingToEveryOrderOver(
        PromotionInterface $promotion,
        float $discount,
        int $itemTotal,
    ): void {
        $channelCode = $this->getChannelCode();
        $rule = $this->ruleFactory->createItemTotal($channelCode, $itemTotal);
        $action = $this->actionFactory->createShippingPercentageDiscount($discount);

        $this->persistPromotion($promotion, $action, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives free shipping to every order over ("(?:€|£|\$)[^"]+")$/
     */
    public function itGivesFreeShippingToEveryOrderOver(PromotionInterface $promotion, int $itemTotal): void
    {
        $this->itGivesDiscountOnShippingToEveryOrderOver($promotion, 1, $itemTotal);
    }

    /**
     * @Given /^I have generated (\d+) coupons for (this promotion) with code length (\d+) and prefix "([^"]+)"$/
     * @Given /^I have generated (\d+) coupons for (this promotion) with code length (\d+), prefix "([^"]+)" and suffix "([^"]+)"$/
     */
    public function iHaveGeneratedCouponsForThisPromotionWithCodeLengthPrefixAndSuffix(
        int $amount,
        PromotionInterface $promotion,
        int $codeLength,
        string $prefix,
        ?string $suffix = null,
    ): void {
        $this->generateCoupons($amount, $promotion, $codeLength, $prefix, $suffix);
    }

    /**
     * @Given /^I have generated (\d+) coupons for (this promotion) with code length (\d+) and suffix "([^"]+)"$/
     */
    public function iHaveGeneratedCouponsForThisPromotionWithCodeLengthAndSuffix(
        int $amount,
        PromotionInterface $promotion,
        int $codeLength,
        string $suffix,
    ): void {
        $this->generateCoupons($amount, $promotion, $codeLength, null, $suffix);
    }

    /**
     * @Given /^(this promotion) is not available in any channel$/
     */
    public function thisPromotionIsNotAvailableInAnyChannel(PromotionInterface $promotion): void
    {
        /** @var ChannelInterface $channel */
        foreach ($promotion->getChannels() as $channel) {
            $promotion->removeChannel($channel);
        }

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) has usage limit equal to (\d+)$/
     */
    public function thisPromotionHasUsageLimitEqualTo(PromotionInterface $promotion, int $usageLimit): void
    {
        $promotion->setUsageLimit($usageLimit);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this promotion) usage limit is already reached$/
     */
    public function thisPromotionUsageLimitIsAlreadyReached(PromotionInterface $promotion): void
    {
        $promotion->setUsed($promotion->getUsageLimit());

        $this->objectManager->flush();
    }

    private function getTaxonFilterConfiguration(array $taxonCodes): array
    {
        return ['filters' => ['taxons_filter' => ['taxons' => $taxonCodes]]];
    }

    private function getProductsFilterConfiguration(array $productCodes): array
    {
        return ['filters' => ['products_filter' => ['products' => $productCodes]]];
    }

    private function getPriceRangeFilterConfiguration(int $minAmount, int $maxAmount = null): array
    {
        $configuration = ['filters' => ['price_range_filter' => ['min' => $minAmount]]];
        if (null !== $maxAmount) {
            $configuration['filters']['price_range_filter']['max'] = $maxAmount;
        }

        return $configuration;
    }

    private function createPromotion(string $name, ?string $code = null): PromotionInterface
    {
        $promotion = $this->testPromotionFactory->createForChannel($name, $this->sharedStorage->get('channel'));

        if (null !== $code) {
            $promotion->setCode($code);
        }

        $this->promotionRepository->add($promotion);
        $this->sharedStorage->set('promotion', $promotion);

        return $promotion;
    }

    private function createUnitFixedPromotion(
        PromotionInterface $promotion,
        int $discount,
        array $configuration = [],
        PromotionRuleInterface $rule = null,
    ): void {
        $channelCode = $this->getChannelCode();

        $this->persistPromotion(
            $promotion,
            $this->actionFactory->createUnitFixedDiscount($discount, $channelCode),
            [$channelCode => $configuration],
            $rule,
        );
    }

    private function createUnitPercentagePromotion(
        PromotionInterface $promotion,
        float $discount,
        array $configuration = [],
        PromotionRuleInterface $rule = null,
    ): void {
        $channelCode = $this->getChannelCode();

        $this->persistPromotion(
            $promotion,
            $this->actionFactory->createUnitPercentageDiscount($discount, $channelCode),
            [$channelCode => $configuration],
            $rule,
        );
    }

    private function createFixedPromotion(
        PromotionInterface $promotion,
        int $discount,
        array $configuration = [],
        PromotionRuleInterface $rule = null,
        ChannelInterface $channel = null,
    ): void {
        $channelCode = (null !== $channel) ? $channel->getCode() : $this->sharedStorage->get('channel')->getCode();

        $this->persistPromotion($promotion, $this->actionFactory->createFixedDiscount($discount, $channelCode), $configuration, $rule);
    }

    private function createPercentagePromotion(
        PromotionInterface $promotion,
        float $discount,
        array $configuration = [],
        PromotionRuleInterface $rule = null,
    ): void {
        $this->persistPromotion($promotion, $this->actionFactory->createPercentageDiscount($discount), $configuration, $rule);
    }

    private function persistPromotion(
        PromotionInterface $promotion,
        PromotionActionInterface $action,
        array $configuration,
        PromotionRuleInterface $rule = null,
    ): void {
        $configuration = array_merge_recursive($action->getConfiguration(), $configuration);
        $action->setConfiguration($configuration);

        $promotion->addAction($action);
        if (null !== $rule) {
            $promotion->addRule($rule);
        }

        $this->objectManager->flush();
    }

    private function createCoupon(string $couponCode, ?int $usageLimit = null): PromotionCouponInterface
    {
        /** @var PromotionCouponInterface $coupon */
        $coupon = $this->couponFactory->createNew();
        $coupon->setCode($couponCode);
        $coupon->setUsageLimit($usageLimit);

        return $coupon;
    }

    private function generateCoupons(
        int $amount,
        PromotionInterface $promotion,
        int $codeLength,
        ?string $prefix = null,
        ?string $suffix = null,
    ): void {
        $instruction = new PromotionCouponGeneratorInstruction();
        $instruction->setAmount($amount);
        $instruction->setCodeLength($codeLength);
        $instruction->setPrefix($prefix);
        $instruction->setSuffix($suffix);

        $this->couponGenerator->generate($promotion, $instruction);
    }

    private function getChannelCode(): string
    {
        return $this->sharedStorage->get('channel')->getCode();
    }
}
