<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\Adjustment;
use Sylius\Component\Order\Model\OrderItem;
use Sylius\Component\Product\Model\Product;
use Sylius\Component\Promotion\Filter\FilterInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Model\BenefitInterface;

class PromotionContext extends DefaultContext
{
    /**
     * @var OrderInterface
     */
    private $order;

    /**
     * @Given /^promotion "([^""]*)" has following coupons defined:$/
     * @Given /^promotion "([^""]*)" has following coupons:$/
     */
    public function theFollowingPromotionCouponsAreDefined($name, TableNode $table)
    {
        $promotion = $this->findOneByName('promotion', $name);

        $manager = $this->getEntityManager();
        $factory = $this->getFactory('promotion_coupon');

        foreach ($table->getHash() as $data) {
            $coupon = $factory->createNew();
            $coupon->setCode($data['code']);
            $coupon->setUsageLimit(isset($data['usage limit']) ? $data['usage limit'] : 0);
            $coupon->setUsed(isset($data['used']) ? $data['used'] : 0);

            $promotion->addCoupon($coupon);

            $manager->persist($coupon);
        }

        $promotion->setCouponBased(true);

        $manager->flush();
    }

    /**
     * @Given /^promotion "([^""]*)" has following rules defined:$/
     */
    public function theFollowingPromotionRulesAreDefined($name, TableNode $table)
    {
        $promotion = $this->findOneByName('promotion', $name);

        $manager = $this->getEntityManager();
        $factory = $this->getFactory('promotion_rule');

        foreach ($table->getHash() as $data) {
            $configuration = $this->cleanPromotionConfiguration($this->getConfiguration($data['configuration']));

            $rule = $factory->createNew();
            $rule->setType(strtolower(str_replace(' ', '_', $data['type'])));
            $rule->setConfiguration($configuration);

            $promotion->addRule($rule);

            $manager->persist($rule);
        }

        $manager->flush();
    }

    /**
     * @Given /^promotion "([^""]*)" has following filters defined:$/
     */
    public function theFollowingPromotionFiltersAreDefined($promotionName, TableNode $table)
    {
        /** @var PromotionInterface $promotion */
        $promotion = $this->findOneByName('promotion', $promotionName);

        $manager = $this->getEntityManager();
        $filterFactory = $this->getFactory('promotion_filter');

        foreach ($table->getHash() as $filterData) {
            $actionNumber = $this->getActionNumber($filterData);
            $action = $this->getOrCreateAction($promotion, $actionNumber);

            $configuration = [];

            if (!empty($filterData['configuration'])) {
                $configuration = $this->getConfiguration($filterData['configuration']);
            }

            $filterType = $filterData['type'];

            /** @var FilterInterface $filter */
            $filter = $filterFactory->createNew();
            $filter->setType($filterType);
            $filter->setConfiguration($configuration);

            $action->addFilter($filter);
        }
    }

    /**
     * @Given /^promotion "([^""]*)" has following benefits defined:$/
     */
    public function theFollowingPromotionBenefitsAreDefined($promotionName, TableNode $table)
    {
        /** @var PromotionInterface $promotion */
        $promotion = $this->findOneByName('promotion', $promotionName);
        $benefitFactory = $this->getFactory('promotion_benefit');

        foreach ($table->getHash() as $benefitData) {
            $actionNumber = $this->getActionNumber($benefitData);

            $action = $this->getOrCreateAction($promotion, $actionNumber);

            $configuration = $this->cleanPromotionConfiguration($this->getConfiguration($benefitData['configuration']));
            $benefitType = strtolower(str_replace(' ', '_', $benefitData['type']));

            /** @var BenefitInterface $benefit */
            $benefit = $benefitFactory->createNew();
            $benefit->setType($benefitType);


            $benefit->setType($benefitType);
            $benefit->setConfiguration($configuration);

            $action->addBenefit($benefit);

            $promotion->addAction($action);
            $action->setPromotion($promotion);
        }

        $this->getEntityManager()->persist($promotion);
        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^the following promotions exist:$/
     * @Given /^there are following promotions configured:$/
     */
    public function theFollowingPromotionsExist(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $factory = $this->getFactory('promotion');

        foreach ($table->getHash() as $data) {
            /** @var PromotionInterface $promotion */
            $promotion = $factory->createNew();

            $promotion->setName($data['name']);
            $promotion->setDescription($data['description']);
            $promotion->setCode($data['code']);

            if (isset($data['not-active']) && '' == $data['not-active']) {
                $promotion->setStartsAt($this->faker->dateTime('-2 years'));
                $promotion->setEndsAt($this->faker->dateTime('-1 year'));
            } else {
                $promotion->setStartsAt($this->faker->dateTimeBetween('-30 years', 'now'));
                $promotion->setEndsAt($this->faker->dateTimeBetween('now', '+ 30 years'));
            }

            if (isset($data['usage limit']) && '' !== $data['usage limit']) {
                $promotion->setUsageLimit((int) $data['usage limit']);
            }
            if (isset($data['used']) && '' !== $data['used']) {
                $promotion->setUsed((int) $data['used']);
            }
            if (isset($data['starts'])) {
                $promotion->setStartsAt(new \DateTime($data['starts']));
            }
            if (isset($data['ends'])) {
                $promotion->setEndsAt(new \DateTime($data['ends']));
            }

            $manager->persist($promotion);
        }

        $manager->flush();
    }

    /**
     * @Given /^Order is shipped to "([^""]*)"$/
     */
    public function orderIsShippedTo($countryName)
    {
        $country = $this->getRepository('country')->findOneBy([
            'isoName' => $this->getCountryCodeByEnglishCountryName($countryName)
        ]);

        /** @var AddressInterface $address */
        $address = $this->getFactory('address')->createNew();
        $address->setCountry($country);

        $this->order->setShippingAddress($address);
    }

    /**
     * @Given /^Promotion "([^""]*)" is active$/
     */
    public function promotionIsActive($promotionName)
    {
        $promotion = $this->findOneByName('promotion', $promotionName);

        $promotion->setStartsAt($this->faker->dateTimeBetween('-30 years', 'now'));
        $promotion->setEndsAt($this->faker->dateTimeBetween('now', '+ 30 years'));

        $manager = $this->getEntityManager();
        $manager->persist($promotion);
        $manager->flush();
    }

    /**
     * @Given /^I have empty order$/
     */
    public function emptyOrder()
    {
        $this->order = $this->getFactory('order')->createNew();
        $this->order->setCurrency('123');
    }

    /**
     * @Given /^I add "([^""]*)" product of "([^""]*)" type$/
     */
    public function addProductsToOrder($quantity, $productName)
    {
        /** @var Product $product */
        $product = $this->getContainer()
            ->get('sylius.repository.product')
            ->findOneBy(['name' => $productName]);

        /** @var OrderItem $orderItem */
        $orderItem = $this->getFactory('order_item')->createNew();

        $priceCalculator = $this->getContainer()->get('sylius.price_calculator');

        $price = $priceCalculator->calculate($product->getMasterVariant());

        $orderItem->setUnitPrice($price);
        $orderItem->setQuantity($quantity);
        $orderItem->setVariant($product->getMasterVariant());
        $this->order->addItem($orderItem);
        $orderItem->setOrder($this->order);

        $this->order->calculateTotal();
    }

    /**
     * @When /^I apply promotions$/
     */
    public function applyPromotions()
    {
        $promotionProcessor = $this->getContainer()->get('sylius.promotion_processor');
        $promotionProcessor->process($this->order);
        $this->order->calculateTotal();
    }

    /**
     * @Then /^I should have no discount$/
     */
    public function shouldBeNoDiscount()
    {
        \PHPUnit_Framework_Assert::assertEquals(
            0,
            $this->order->getAdjustmentsTotal(),
            sprintf(
                'Expected no discount, discount found with value: %s', $this->order->getAdjustmentsTotal()
            )
        );
    }

    /**
     * @Then /^I should have "([^""]*)" discount equal ([^""]*)$/
     */
    public function shouldBeADiscount($promotionName, $discountValue)
    {
        if (!$promotionName) {
            $this->shouldBeNoDiscount();
            return;
        }

        $originator = $this->getContainer()->get('sylius.originator');

        $discounts = explode(',', $promotionName);
        $totalDiscountFound = 0;

        foreach ($discounts as $promotionName) {
            $discountExist = false;

            foreach ($this->order->getItems() as $item) {

                /** @var Adjustment $adjustment */
                foreach ($item->getAdjustments('promotion') as $adjustment) {
                    $origin = $originator->getOrigin($adjustment);

                    if ($origin && $origin->getName() == $promotionName) {
                        $discountExist = true;
                        $totalDiscountFound += $adjustment->getAmount();
                    }
                }
            }
        }

        if (!$discountExist) {
            throw new \Exception(
                sprintf(
                    'Expected discount with name: %s not found', $promotionName)
            );
        }

        \PHPUnit_Framework_Assert::assertSame(
            $this->normalizePrice($discountValue),
            $totalDiscountFound,
            sprintf('Promotion(s): "%s" found but discounts do not match, actual discount: %s',
                $promotionName,
                $totalDiscountFound
            )
        );
    }

    /**
     * @Then /^Total price should be ([^""]*)$/
     */
    public function totalPriceShouldBe($totalPrice)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            $this->normalizePrice($totalPrice),
            $this->order->getTotal(),
            'Expected different total price'
        );
    }

    /**
     * @Given /^I add ([^""]*) to the order$/
     *
     * @param TableNode $basketContent
     */
    public function orderShouldContain($basketContent)
    {
        foreach (explode(',', $basketContent) as $product) {
            list($productName, $productQuantity) = explode(':', $product);

            $this->addProductsToOrder($productQuantity, $productName);
        }
    }

    /**
     * @Given /^I have "([^""]*)" promotions activated$/
     *
     * @param TableNode $promotions
     */
    public function givenPromotionsShouldBeActive($promotions)
    {
        foreach (explode(',', $promotions) as $promotionName) {
            $this->promotionIsActive($promotionName);
        }
    }

    /**
     * @Then I should see product :productName in the cart summary
     */
    public function iShouldSeeProductInCartSummary($productName)
    {
        $cartSummary = $this->getCartSummaryPageElement();
        if (false === strpos($cartSummary->getText(), $productName)) {
            throw new \InvalidArgumentException(sprintf('Product "%s" was not found in cart summary', $productName));
        }
    }

    /**
     * @Then I should not see product :productName in the cart summary
     */
    public function iShouldNotSeeProductInCartSummary($productName)
    {
        $cartSummary = $this->getCartSummaryPageElement();
        if (false !== strpos($cartSummary->getText(), $productName)) {
            throw new \InvalidArgumentException(sprintf('Product "%s" was found in cart summary', $productName));
        }
    }

    /**
     * @return NodeElement
     *
     * @throws \Exception
     */
    private function getCartSummaryPageElement()
    {
        $element = $this->getSession()->getPage()->find('css', 'div:contains("Cart summary") > form > table');
        if (null === $element) {
            throw new \Exception("Cart summary element cannot be found!");
        }

        return $element;
    }

    /**
     * Cleaning promotion configuration that is serialized in database.
     *
     * @param array $configuration
     *
     * @return array
     */
    private function cleanPromotionConfiguration(array $configuration)
    {
        foreach ($configuration as $key => $value) {
            switch ($key) {
                case 'amount':
                case 'price':
                    $configuration[$key] = (int) $value * 100;
                    break;
                case 'count':
                    $configuration[$key] = (int) $value;
                    break;
                case 'percentage':
                    $configuration[$key] = (int) $value / 100;
                    break;
                case 'equal':
                    $configuration[$key] = (Boolean) $value;
                    break;
                default:
                    break;
            }
        }

        return $configuration;
    }

    /**
     * @param float $price
     *
     * @return int
     */
    private function normalizePrice($price)
    {
        return (int) round($price * 100);
    }

    /**
     * @param array $data
     *
     * @return int
     */
    private function getActionNumber($data)
    {
        $actionNumber = 1;

        if (isset($data['actionNumber'])) {
            $actionNumber = $data['actionNumber'];
        }

        return $actionNumber;
    }

    /**
     * @param PromotionInterface $promotion
     * @param int $actionNumber
     *
     * @return ActionInterface
     */
    private function getOrCreateAction(PromotionInterface $promotion, $actionNumber)
    {
        if (!$promotion->getActions()->offsetExists($actionNumber)) {
            /** @var ActionInterface $action */
            $action = $this->getFactory('promotion_action')->createNew();
            $promotion->getActions()->offsetSet($actionNumber, $action);
        } else {
            $action = $promotion->getActions()->offsetGet($actionNumber);
        }

        return $action;
    }
}
