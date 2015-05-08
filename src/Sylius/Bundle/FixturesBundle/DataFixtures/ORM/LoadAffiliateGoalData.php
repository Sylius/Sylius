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
use Sylius\Component\Affiliate\Model\ActionInterface;
use Sylius\Component\Affiliate\Model\GoalInterface;
use Sylius\Component\Affiliate\Model\RuleInterface;

/**
 * Default affiliate goal fixtures.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class LoadAffiliateGoalData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $goal = $this->createGoal(
            'Newsletter Sign-up',
            'Provision for newsletter sign-up.',
            array($this->createRule(RuleInterface::TYPE_NTH, array('count' => 1))),
            array($this->createAction(ActionInterface::TYPE_FIXED_PROVISION, array('amount' => 5000)))
        );

        $manager->persist($goal);

        $goal = $this->createGoal(
            'Registration',
            'Provision for registration.',
            array($this->createRule(RuleInterface::TYPE_NTH, array('count' => 1))),
            array($this->createAction(ActionInterface::TYPE_FIXED_PROVISION, array('amount' => 2500)))
        );

        $manager->persist($goal);

        $goal = $this->createGoal(
            '1st order',
            'Provision for 1st order',
            array($this->createRule(RuleInterface::TYPE_NTH, array('count' => 1))),
            array($this->createAction(ActionInterface::TYPE_PERCENTAGE_PROVISION, array('amount' => 5)))
        );

        $manager->persist($goal);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 6;
    }

    /**
     * Create affiliate goal rule of given type and configuration.
     *
     * @param string $type
     * @param array  $configuration
     *
     * @return RuleInterface
     */
    protected function createRule($type, array $configuration)
    {
        /** @var $rule RuleInterface */
        $rule = $this->getAffiliateRuleRepository()->createNew();
        $rule->setType($type);
        $rule->setConfiguration($configuration);

        return $rule;
    }

    /**
     * Create affiliate goal action of given type and configuration.
     *
     * @param string $type
     * @param array  $configuration
     *
     * @return ActionInterface
     */
    protected function createAction($type, array $configuration)
    {
        /** @var $action ActionInterface */
        $action = $this->getAffiliateActionRepository()->createNew();
        $action->setType($type);
        $action->setConfiguration($configuration);

        return $action;
    }

    /**
     * Create affiliate goal with set of rules and actions.
     *
     * @param string $name
     * @param string $description
     * @param array  $rules
     * @param array  $actions
     *
     * @return GoalInterface
     */
    protected function createGoal($name, $description, array $rules, array $actions)
    {
        /** @var $goal GoalInterface */
        $goal = $this->getAffiliateGoalRepository()->createNew();
        $goal->setName($name);
        $goal->setDescription($description);

        foreach ($rules as $rule) {
            $goal->addRule($rule);
        }
        foreach ($actions as $action) {
            $goal->addAction($action);
        }

        $this->setReference('Sylius.AffiliateGoal.'.$name, $goal);

        return $goal;
    }
}
