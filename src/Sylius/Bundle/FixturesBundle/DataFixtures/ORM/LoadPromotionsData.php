<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Core\Model\PromotionRuleInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * Default promotion fixtures.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LoadPromotionsData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $promotion = $this->createPromotion(
            'New Year',
            'New Year Sale for 3 and more items.',
            array($this->createRule(PromotionRuleInterface::TYPE_ITEM_COUNT, array('count' => 3, 'equal' => true))),
            array($this->createAction(ActionInterface::TYPE_FIXED_DISCOUNT, array('amount' => 500)))
        );

        $manager->persist($promotion);

        $promotion = $this->createPromotion(
            'Christmas',
            'Christmas Sale for orders over 100 EUR.',
            array($this->createRule(PromotionRuleInterface::TYPE_ITEM_TOTAL, array('amount' => 10000, 'equal' => true))),
            array($this->createAction(ActionInterface::TYPE_FIXED_DISCOUNT, array('amount' => 250)))
        );

        $manager->persist($promotion);

        $promotion = $this->createPromotion(
            '3rd order',
            'Discount for 3rd order',
            array($this->createRule(PromotionRuleInterface::TYPE_NTH_ORDER, array('nth' => 3))),
            array($this->createAction(ActionInterface::TYPE_FIXED_DISCOUNT, array('amount' => 500)))
        );

        $manager->persist($promotion);

        $promotion = $this->createPromotion(
            'Shipping to Germany',
            'Discount for orders with shipping country Germany',
            array($this->createRule(PromotionRuleInterface::TYPE_SHIPPING_COUNTRY, array('country' => $this->getReference('Sylius.Country.DE')->getId()))),
            array($this->createAction(ActionInterface::TYPE_FIXED_DISCOUNT, array('amount' => 500)))
        );

        $manager->persist($promotion);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 20;
    }

    /**
     * Create promotion rule of given type and configuration.
     *
     * @param string $type
     * @param array  $configuration
     *
     * @return PromotionRuleInterface
     */
    protected function createRule($type, array $configuration)
    {
        /** @var $rule PromotionRuleInterface */
        $rule = $this->getPromotionRuleRepository()->createNew();
        $rule->setType($type);
        $rule->setConfiguration($configuration);

        return $rule;
    }

    /**
     * Create promotion action of given type and configuration.
     *
     * @param string $type
     * @param array  $configuration
     *
     * @return ActionInterface
     */
    protected function createAction($type, array $configuration)
    {
        /** @var $action ActionInterface */
        $action = $this->getPromotionActionRepository()->createNew();
        $action->setType($type);
        $action->setConfiguration($configuration);

        return $action;
    }

    /**
     * Create promotion with set of rules and actions.
     *
     * @param string $name
     * @param string $description
     * @param array  $rules
     * @param array  $actions
     *
     * @return PromotionInterface
     */
    protected function createPromotion($name, $description, array $rules, array $actions)
    {
        /** @var $promotion PromotionInterface */
        $promotion = $this->getPromotionRepository()->createNew();
        $promotion->setName($name);
        $promotion->setDescription($description);

        foreach ($rules as $rule) {
            $promotion->addRule($rule);
        }
        foreach ($actions as $action) {
            $promotion->addAction($action);
        }

        $this->setReference('Sylius.Promotion.'.$name, $promotion);

        return $promotion;
    }
}
