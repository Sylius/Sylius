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

class PromotionContext extends DefaultContext
{
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
     * @Given /^promotion "([^""]*)" has following actions defined:$/
     */
    public function theFollowingPromotionActionsAreDefined($name, TableNode $table)
    {
        $promotion = $this->findOneByName('promotion', $name);

        $manager = $this->getEntityManager();
        $factory = $this->getFactory('promotion_action');

        foreach ($table->getHash() as $data) {
            $configuration = $this->cleanPromotionConfiguration($this->getConfiguration($data['configuration']));

            $action = $factory->createNew();
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
        $factory = $this->getFactory('promotion');

        foreach ($table->getHash() as $data) {
            $promotion = $factory->createNew();

            $promotion->setName($data['name']);
            $promotion->setDescription($data['description']);
            $promotion->setCode($data['code']);

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
     * @Given /^I add "([^"]*)" action$/
     */
    public function iAddAction($action)
    {
        $this->clickLink('Add action');

        $this->getSession()->wait(100);

        $this->selectOption('Type', $action);
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
            throw new \Exception('Cart summary element cannot be found!');
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
                default:
                    break;
            }
        }

        return $configuration;
    }
}
