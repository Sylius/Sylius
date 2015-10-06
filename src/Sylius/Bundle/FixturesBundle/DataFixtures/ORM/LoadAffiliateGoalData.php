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
use Sylius\Component\Affiliate\Model\ProvisionInterface;
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
            'Registration',
            'Provision for registration.',
            array($this->createRule(RuleInterface::TYPE_REGISTRATION, array('count' => 1))),
            array($this->createProvision(ProvisionInterface::TYPE_FIXED_PROVISION, array('amount' => 2500)))
        );

        $manager->persist($goal);

        $goal = $this->createGoal(
            '1st order',
            'Provision for 1st order',
            array($this->createRule(RuleInterface::TYPE_NTH_ORDER, array('count' => 1))),
            array($this->createProvision(ProvisionInterface::TYPE_PERCENTAGE_PROVISION, array('amount' => 5)))
        );

        $manager->persist($goal);

        $goal = $this->createGoal(
            'Visit cart',
            'Provision for visiting the /cart uri.',
            array($this->createRule(RuleInterface::TYPE_URI_VISIT, array('uri' => '/cart'))),
            array($this->createProvision(ProvisionInterface::TYPE_FIXED_PROVISION, array('amount' => 5000)))
        );

        $manager->persist($goal);
        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 60;
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
     * Create affiliate goal provision of given type and configuration.
     *
     * @param string $type
     * @param array  $configuration
     *
     * @return ProvisionInterface
     */
    protected function createProvision($type, array $configuration)
    {
        /** @var $provision ProvisionInterface */
        $provision = $this->getAffiliateProvisionRepository()->createNew();
        $provision->setType($type);
        $provision->setConfiguration($configuration);

        return $provision;
    }

    /**
     * Create affiliate goal with set of rules and provisions.
     *
     * @param string $name
     * @param string $description
     * @param array  $rules
     * @param array  $provisions
     *
     * @return GoalInterface
     */
    protected function createGoal($name, $description, array $rules, array $provisions)
    {
        /** @var $goal GoalInterface */
        $goal = $this->getAffiliateGoalRepository()->createNew();
        $goal->setName($name);
        $goal->setDescription($description);

        foreach ($rules as $rule) {
            $goal->addRule($rule);
        }
        foreach ($provisions as $provision) {
            $goal->addProvision($provision);
        }

        $this->setReference('Sylius.AffiliateGoal.'.$name, $goal);

        return $goal;
    }
}
