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

class PromotionContext extends DefaultContext
{
    /**
     * @Given /^promotion "([^""]*)" has following coupons defined:$/
     * @Given /^promotion "([^""]*)" has following coupons:$/
     */
    public function theFollowingPromotionCouponsAreDefined($name, TableNode $table)
    {
        $promotion = $this->findOneByName('promotion', $name);

        $manager = $this->getManager('promotion_coupon');
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

        $manager = $this->getManager('promotion_rule');
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

        $manager = $this->getManager('promotion_action');
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
        $manager = $this->getManager('promotion');
        $factory = $this->getFactory('promotion');

        foreach ($table->getHash() as $data) {
            $promotion = $factory->createNew();

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
}
