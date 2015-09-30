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
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Order\Model\Adjustment;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItem;
use Sylius\Component\Product\Model\Product;

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
        $repository = $this->getRepository('promotion_coupon');

        foreach ($table->getHash() as $data) {
            $coupon = $repository->createNew();
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
        $repository = $this->getRepository('promotion_rule');

        foreach ($table->getHash() as $data) {
            $configuration = $this->cleanPromotionConfiguration($this->getConfiguration($data['configuration']));

            $rule = $repository->createNew();
            $rule->setType(strtolower(str_replace(' ', '_', $data['type'])));
            $rule->setConfiguration($configuration);

            $promotion->addRule($rule);

            $manager->persist($rule);
        }

        $manager->flush();
    }

    /**
     * @Given /^promotion "([^""]*)" has following actions defined:$/
     */
    public function theFollowingPromotionActionsAreDefined($name, TableNode $table)
    {
        $promotion = $this->findOneByName('promotion', $name);

        $manager = $this->getEntityManager();
        $repository = $this->getRepository('promotion_action');

        foreach ($table->getHash() as $data) {
            $configuration = $this->cleanPromotionConfiguration($this->getConfiguration($data['configuration']));

            $action = $repository->createNew();
            $action->setType(strtolower(str_replace(' ', '_', $data['type'])));
            $action->setConfiguration($configuration);

            $promotion->addAction($action);

            $manager->persist($action);
        }

        $manager->flush();
    }

    /**
     * @Given /^the following promotions exist:$/
     * @Given /^there are following promotions configured:$/
     */
    public function theFollowingPromotionsExist(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('promotion');

        foreach ($table->getHash() as $data) {
            $promotion = $repository->createNew();

            $promotion->setName($data['name']);
            $promotion->setDescription($data['description']);

            if (array_key_exists('usage limit', $data) && '' !== $data['usage limit']) {
                $promotion->setUsageLimit((int) $data['usage limit']);
            }
            if (array_key_exists('used', $data) && '' !== $data['used']) {
                $promotion->setUsed((int) $data['used']);
            }
            if (array_key_exists('starts', $data)) {
                $promotion->setStartsAt(new \DateTime($data['starts']));
            }
            if (array_key_exists('ends', $data)) {
                $promotion->setEndsAt(new \DateTime($data['ends']));
            }

            $manager->persist($promotion);
        }

        $manager->flush();
    }

    /**
     * @Given /^I have empty order$/
     */
    public function emptyOrder()
    {
        $this->order = $this->getContainer()->get('sylius.repository.order')->createNew();
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
        $orderItem = $this->getContainer()->get('sylius.repository.order_item')->createNew();

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
    public function shouldBeADiscount($discountName, $discountValue)
    {
        $discountExist = false;

        /** @var Adjustment $promotion */
        foreach ($this->order->getAdjustments('promotion') as $promotion)
        {
            if (
                $promotion->getDescription() == $discountName
            ) {
                $discountExist = true;

                \PHPUnit_Framework_Assert::assertSame(
                   $this->normalizePrice($discountValue),
                   $promotion->getAmount(),
                   sprintf('Promotion: "%s" found but discount do not match, actual discount: %s',
                        $discountName,
                        $promotion->getAmount()
                   )
                );

                break;
            }
        }

        if (!$discountExist) {
            throw new \Exception(
                sprintf(
                    'Expected discount with name: %s not found',
                    $discountName, $discountValue)
            );
        }
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

    private function normalizePrice($price)
    {
        return (int) round($price * 100);
    }
}
