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
use Sylius\Component\Core\Model\PromotionInterface;

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
        $channel = 'DEFAULT';

        $promotion = $this->createPromotion(
            'PR1',
            'New Year',
            'New Year Sale for 3 and more items.',
            3,
            $channel,
            [$this->createRule(PromotionRuleInterface::TYPE_CART_QUANTITY, ['count' => 3, 'equal' => true])],
            [$this->createAction(ActionInterface::TYPE_FIXED_DISCOUNT, ['amount' => 500])]
        );

        $manager->persist($promotion);

        $promotion = $this->createPromotion(
            'PR2',
            'Christmas',
            'Christmas Sale for orders over 100 EUR.',
            2,
            $channel,
            [$this->createRule(PromotionRuleInterface::TYPE_ITEM_TOTAL, ['amount' => 10000, 'equal' => true])],
            [$this->createAction(ActionInterface::TYPE_FIXED_DISCOUNT, ['amount' => 250])]
        );

        $manager->persist($promotion);

        $promotion = $this->createPromotion(
            'PR3',
            '3rd order',
            'Discount for 3rd order',
            1,
            $channel,
            [$this->createRule(PromotionRuleInterface::TYPE_NTH_ORDER, ['nth' => 3])],
            [$this->createAction(ActionInterface::TYPE_FIXED_DISCOUNT, ['amount' => 500])]
        );

        $manager->persist($promotion);

        $promotion = $this->createPromotion(
            'PR4',
            'Shipping to Germany',
            'Discount for orders with shipping country Germany',
            0,
            $channel,
            [$this->createRule(PromotionRuleInterface::TYPE_SHIPPING_COUNTRY, ['country' => $this->getReference('Sylius.Country.DE')->getId()])],
            [$this->createAction(ActionInterface::TYPE_FIXED_DISCOUNT, ['amount' => 500])]
        );

        $manager->persist($promotion);

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 50;
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
        $rule = $this->getPromotionRuleFactory()->createNew();
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
        $action = $this->getPromotionActionFactory()->createNew();
        $action->setType($type);
        $action->setConfiguration($configuration);

        return $action;
    }

    /**
     * Create promotion with set of rules and actions.
     *
     * @param string $code
     * @param string $name
     * @param string $description
     * @param int $priority
     * @param string $channel
     * @param array $rules
     * @param array $actions
     *
     * @return PromotionInterface
     */
    protected function createPromotion($code, $name, $description, $priority, $channel, array $rules, array $actions)
    {
        /** @var $promotion PromotionInterface */
        $promotion = $this->getPromotionFactory()->createNew();
        $promotion->setName($name);
        $promotion->setDescription($description);
        $promotion->setCode($code);
        $promotion->setPriority($priority);

        $promotion->addChannel($this->getReference('Sylius.Channel.'.$channel));

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
