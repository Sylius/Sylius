<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Factory\ActionFactoryInterface;
use Sylius\Component\Core\Factory\RuleFactoryInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Test\Factory\TestPromotionFactoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Promotion\Factory\CouponFactoryInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class PromotionContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ActionFactoryInterface
     */
    private $actionFactory;

    /**
     * @var CouponFactoryInterface
     */
    private $couponFactory;

    /**
     * @var RuleFactoryInterface
     */
    private $ruleFactory;

    /**
     * @var TestPromotionFactoryInterface
     */
    private $testPromotionFactory;

    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ActionFactoryInterface $actionFactory
     * @param CouponFactoryInterface $couponFactory
     * @param RuleFactoryInterface $ruleFactory
     * @param TestPromotionFactoryInterface $testPromotionFactory
     * @param PromotionRepositoryInterface $promotionRepository
     * @param ObjectManager $objectManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ActionFactoryInterface $actionFactory,
        CouponFactoryInterface $couponFactory,
        RuleFactoryInterface $ruleFactory,
        TestPromotionFactoryInterface $testPromotionFactory,
        PromotionRepositoryInterface $promotionRepository,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->actionFactory = $actionFactory;
        $this->couponFactory = $couponFactory;
        $this->ruleFactory = $ruleFactory;
        $this->testPromotionFactory = $testPromotionFactory;
        $this->promotionRepository = $promotionRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @Given there is a promotion :promotionName
     * @Given there is a promotion :promotionName identified by :promotionCode code
     */
    public function thereIsPromotion($promotionName, $promotionCode = null)
    {
        $promotion = $this->testPromotionFactory
            ->createForChannel($promotionName, $this->sharedStorage->get('channel'))
        ;

        if (null !== $promotionCode) {
            $promotion->setCode($promotionCode);
        }

        $this->promotionRepository->add($promotion);
        $this->sharedStorage->set('promotion', $promotion);
    }

    /**
     * @Given the store has promotion :promotionName with coupon :couponCode
     * @Given the store has also promotion :promotionName with coupon :couponCode
     */
    public function thereIsPromotionWithCoupon($promotionName, $couponCode)
    {
        /** @var CouponInterface $coupon */
        $coupon = $this->couponFactory->createNew();
        $coupon->setCode($couponCode);

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
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") discount to every order$/
     */
    public function itGivesFixedDiscountToEveryOrder(PromotionInterface $promotion, $discount)
    {
        $this->createFixedPromotion($promotion, $discount);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") discount to every order$/
     */
    public function itGivesPercentageDiscountToEveryOrder(PromotionInterface $promotion, $discount)
    {
        $this->createPercentagePromotion($promotion, $discount);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") discount to every order with quantity at least ([^"]+)$/
     */
    public function itGivesFixedDiscountToEveryOrderWithQuantityAtLeast(
        PromotionInterface $promotion,
        $discount,
        $quantity
    ) {
        $rule = $this->ruleFactory->createCartQuantity((int) $quantity);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") discount to every order with items total at least ("[^"]+")$/
     */
    public function itGivesFixedDiscountToEveryOrderWithItemsTotalAtLeast(
        PromotionInterface $promotion,
        $discount,
        $targetAmount
    ) {
        $rule = $this->ruleFactory->createItemTotal($targetAmount);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") discount on shipping to every order$/
     */
    public function itGivesPercentageDiscountOnShippingToEveryOrder(PromotionInterface $promotion, $discount)
    {
        $action = $this->actionFactory->createPercentageShippingDiscount($discount);
        $promotion->addAction($action);

        $this->objectManager->flush();
    }

    /**
     * @Given /^([^"]+) gives free shipping to every order$/
     */
    public function thePromotionGivesFreeShippingToEveryOrder(PromotionInterface $promotion)
    {
        $this->itGivesPercentageDiscountOnShippingToEveryOrder($promotion, 1);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off every product (classified as "[^"]+")$/
     */
    public function itGivesPercentageOffEveryProductClassifiedAs(
        PromotionInterface $promotion,
        $discount,
        TaxonInterface $taxon
    ) {
        $this->createUnitPercentagePromotion($promotion, $discount, $this->getTaxonFilterConfiguration([$taxon->getCode()]));
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off on every product (classified as "[^"]+")$/
     */
    public function itGivesFixedOffEveryProductClassifiedAs(
        PromotionInterface $promotion,
        $discount,
        TaxonInterface $taxon
    ) {
        $this->createUnitFixedPromotion($promotion, $discount, $this->getTaxonFilterConfiguration([$taxon->getCode()]));
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off on every product with minimum price at ("(?:€|£|\$)[^"]+")$/
     */
    public function thisPromotionGivesOffOnEveryProductWithMinimumPriceAt(
        PromotionInterface $promotion,
        $discount,
        $amount
    ) {
        $this->createUnitFixedPromotion($promotion, $discount, $this->getPriceRangeFilterConfiguration($amount));
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off on every product priced between ("(?:€|£|\$)[^"]+") and ("(?:€|£|\$)[^"]+")$/
     */
    public function thisPromotionGivesOffOnEveryProductPricedBetween(
        PromotionInterface $promotion,
        $discount,
        $minAmount,
        $maxAmount
    ) {
        $this->createUnitFixedPromotion(
            $promotion,
            $discount,
            $this->getPriceRangeFilterConfiguration($minAmount, $maxAmount)
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product with minimum price at ("(?:€|£|\$)[^"]+")$/
     */
    public function thisPromotionPercentageGivesOffOnEveryProductWithMinimumPriceAt(
        PromotionInterface $promotion,
        $discount,
        $amount
    ) {
        $this->createUnitPercentagePromotion($promotion, $discount, $this->getPriceRangeFilterConfiguration($amount));
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product priced between ("(?:€|£|\$)[^"]+") and ("(?:€|£|\$)[^"]+")$/
     */
    public function thisPromotionPercentageGivesOffOnEveryProductPricedBetween(
        PromotionInterface $promotion,
        $discount,
        $minAmount,
        $maxAmount
    ) {
        $this->createUnitPercentagePromotion(
            $promotion,
            $discount,
            $this->getPriceRangeFilterConfiguration($minAmount, $maxAmount)
        );
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off if order contains products (classified as "[^"]+")$/
     */
    public function thePromotionGivesOffIfOrderContainsProductsClassifiedAs(
        PromotionInterface $promotion,
        $discount,
        TaxonInterface $taxon
    ) {
        $rule = $this->ruleFactory->createTaxon([$taxon->getCode()]);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off if order contains products (classified as "[^"]+" or "[^"]+")$/
     */
    public function thePromotionGivesOffIfOrderContainsProductsClassifiedAsOr(
        PromotionInterface $promotion,
        $discount,
        array $taxons
    ) {
        $rule = $this->ruleFactory->createTaxon([$taxons[0]->getCode(), $taxons[1]->getCode()]);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off if order contains products (classified as "[^"]+") with a minimum value of ("(?:€|£|\$)[^"]+")$/
     */
    public function thePromotionGivesOffIfOrderContainsProductsClassifiedAsAndPricedAt(
        PromotionInterface $promotion,
        $discount,
        TaxonInterface $taxon,
        $amount
    ) {
        $rule = $this->ruleFactory->createItemsFromTaxonTotal($taxon->getCode(), $amount);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off if order contains (\d+) products (classified as "[^"]+")$/
     */
    public function thePromotionGivesOffIfOrderContainsNumberOfProductsClassifiedAs(
        PromotionInterface $promotion,
        $discount,
        $count,
        TaxonInterface $taxon
    ) {
        $rule = $this->ruleFactory->createContainsTaxon($taxon->getCode(), $count);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off customer's (\d)(?:st|nd|rd|th) order$/
     */
    public function itGivesFixedOffCustomersNthOrder(PromotionInterface $promotion, $discount, $nth)
    {
        $rule = $this->ruleFactory->createNthOrder((int) $nth);

        $this->createFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on the customer's (\d)(?:st|nd|rd|th) order$/
     */
    public function itGivesPercentageOffCustomersNthOrder(PromotionInterface $promotion, $discount, $nth)
    {
        $rule = $this->ruleFactory->createNthOrder((int) $nth);

        $this->createPercentagePromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product (classified as "[^"]+") if an order contains any product (classified as "[^"]+")$/
     */
    public function itGivesPercentageOffOnEveryProductClassifiedAsIfAnOrderContainsAnyProductClassifiedAs(
        PromotionInterface $promotion,
        $discount,
        TaxonInterface $discountTaxon,
        TaxonInterface $targetTaxon
    ) {
        $rule = $this->ruleFactory->createContainsTaxon($targetTaxon->getCode(), 1);

        $this->createUnitPercentagePromotion($promotion, $discount, $this->getTaxonFilterConfiguration([$discountTaxon->getCode()]), $rule);
    }

    /**
     * @Given /^([^"]+) gives ("(?:€|£|\$)[^"]+") off on every product (classified as "[^"]+") and a free shipping to every order with items total equal at least ("[^"]+")$/
     */
    public function itGivesOffOnEveryProductClassifiedAsAndAFreeShippingToEveryOrderWithItemsTotalEqualAtLeast(
        PromotionInterface $promotion,
        $discount,
        TaxonInterface $taxon,
        $targetAmount
    ) {
        $freeShippingAction = $this->actionFactory->createPercentageShippingDiscount(1);
        $promotion->addAction($freeShippingAction);

        $rule = $this->ruleFactory->createItemTotal($targetAmount);

        $this->createUnitFixedPromotion($promotion, $discount, [], $rule);
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product (classified as "[^"]+") and a ("(?:€|£|\$)[^"]+") discount to every order with items total equal at least ("(?:€|£|\$)[^"]+")$/
     */
    public function itGivesOffOnEveryProductClassifiedAsAndAFixedDiscountToEveryOrderWithItemsTotalEqualAtLeast(
        PromotionInterface $promotion,
        $taxonDiscount,
        TaxonInterface $taxon,
        $orderDiscount,
        $targetAmount
    ) {
        $orderDiscountAction = $this->actionFactory->createFixedDiscount($orderDiscount);
        $promotion->addAction($orderDiscountAction);

        $rule = $this->ruleFactory->createItemTotal($targetAmount);

        $this->createUnitPercentagePromotion(
            $promotion,
            $taxonDiscount,
            $this->getTaxonFilterConfiguration([$taxon->getCode()]),
            $rule
        );
    }

    /**
     * @Given /^([^"]+) gives ("[^"]+%") off on every product (classified as "[^"]+" or "[^"]+") if order contains any product (classified as "[^"]+" or "[^"]+")$/
     */
    public function itGivesOffOnEveryProductClassifiedAsOrIfOrderContainsAnyProductClassifiedAsOr(
        PromotionInterface $promotion,
        $discount,
        array $discountTaxons,
        array $targetTaxons
    ) {
        $discountTaxonsCodes = [$discountTaxons[0]->getCode(), $discountTaxons[1]->getCode()];
        $targetTaxonsCodes = [$targetTaxons[0]->getCode(), $targetTaxons[1]->getCode()];

        $rule = $this->ruleFactory->createTaxon($targetTaxonsCodes);

        $this->createUnitPercentagePromotion(
            $promotion,
            $discount,
            $this->getTaxonFilterConfiguration($discountTaxonsCodes),
            $rule
        );
    }

    /**
     * @Given /^(it) is coupon based promotion$/
     */
    public function itIsCouponBasedPromotion(PromotionInterface $promotion)
    {
        $promotion->setCouponBased(true);

        $this->objectManager->flush();
    }

    /**
     * @param array $taxonCodes
     *
     * @return array
     */
    private function getTaxonFilterConfiguration(array $taxonCodes)
    {
        return ['filters' => ['taxons' => $taxonCodes]];
    }

    /**
     * @param int $minAmount
     * @param int $maxAmount
     *
     * @return array
     */
    private function getPriceRangeFilterConfiguration($minAmount, $maxAmount = null)
    {
        $configuration = ['filters' => ['price_range' => ['min' => $minAmount]]];
        if (null !== $maxAmount) {
            $configuration['filters']['price_range']['max'] = $maxAmount;
        }

        return $configuration;
    }

    /**
     * @param PromotionInterface $promotion
     * @param int $discount
     * @param array $configuration
     */
    private function createUnitFixedPromotion(PromotionInterface $promotion, $discount, array $configuration = [], $rule = null)
    {
        $this->persistPromotion($promotion, $this->actionFactory->createUnitFixedDiscount($discount), $configuration, $rule);
    }

    /**
     * @param PromotionInterface $promotion
     * @param int $discount
     * @param array $configuration
     */
    private function createUnitPercentagePromotion(PromotionInterface $promotion, $discount, array $configuration = [], $rule = null)
    {
        $this->persistPromotion($promotion, $this->actionFactory->createUnitPercentageDiscount($discount), $configuration, $rule);
    }

    /**
     * @param PromotionInterface $promotion
     * @param int $discount
     * @param array $configuration
     * @param RuleInterface $rule
     */
    private function createFixedPromotion(PromotionInterface $promotion, $discount, array $configuration = [], RuleInterface $rule = null)
    {
        $this->persistPromotion($promotion, $this->actionFactory->createFixedDiscount($discount), $configuration, $rule);
    }

    /**
     * @param PromotionInterface $promotion
     * @param float $discount
     * @param array $configuration
     * @param RuleInterface $rule
     */
    private function createPercentagePromotion(PromotionInterface $promotion, $discount, array $configuration = [], RuleInterface $rule = null)
    {
        $this->persistPromotion($promotion, $this->actionFactory->createPercentageDiscount($discount), $configuration, $rule);
    }

    /**
     * @param PromotionInterface $promotion
     * @param ActionInterface $action
     * @param array $configuration
     * @param RuleInterface|null $rule
     */
    private function persistPromotion(PromotionInterface $promotion, ActionInterface $action, array $configuration, RuleInterface $rule = null)
    {
        $configuration = array_merge($configuration, $action->getConfiguration());
        $action->setConfiguration($configuration);

        $promotion->addAction($action);
        if (null !== $rule) {
            $promotion->addRule($rule);
        }

        $this->objectManager->flush();
    }
}
