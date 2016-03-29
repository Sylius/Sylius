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
use Sylius\Component\Affiliate\Model\AffiliateGoalInterface;
use Sylius\Component\Affiliate\Model\RuleInterface;

/**
 * Default affiliate goal fixtures.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class LoadAffiliateGoalData extends DataFixture
{
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createRules([
            [
                'type' => RuleInterface::TYPE_REGISTRATION,
                'config' => ['count' => 1]
            ],
            [
                'type' => RuleInterface::TYPE_NTH_ORDER,
                'config' => ['count' => 1]
            ],
            [
                'type' => RuleInterface::TYPE_URI_VISIT,
                'config' => ['uri' => '/cart']
            ],
        ]);

        $this->createProvisions([
            [
                'type' => ProvisionInterface::TYPE_FIXED_PROVISION,
                'config' => ['amount' => 2500]
            ],
            [
                'type' => ProvisionInterface::TYPE_PERCENTAGE_PROVISION,
                'config' => ['percentage' => 5]
            ],
            [
                'type' => ProvisionInterface::TYPE_FIXED_PROVISION,
                'config' => ['amount' => 5000]
            ],
        ]);

        $this->createGoals([
            [
                'name' => 'Registration',
                'desc' => 'Provision for registration',
                'rules' => [$this->getReference('Sylius.AffiliateRule.0')],
                'provisions' => [$this->getReference('Sylius.AffiliateProvision.0')],
            ],
            [
                'name' => '1st order',
                'desc' => 'Provision for 1st order',
                'rules' => [$this->getReference('Sylius.AffiliateRule.1')],
                'provisions' => [$this->getReference('Sylius.AffiliateProvision.1')],
            ],
            [
                'name' => 'Visit cart uri',
                'desc' => 'Provision for visiting the /cart uri',
                'rules' => [$this->getReference('Sylius.AffiliateRule.2')],
                'provisions' => [$this->getReference('Sylius.AffiliateProvision.2')],
            ],
        ]);

        $this->createTransactions();
    }

    protected function createRules(array $rules)
    {
        foreach ($rules as $key => $rule) {
            $rule = $this->createRule($rule['type'], $rule['config']);
            $this->setReference('Sylius.AffiliateRule.' . $key, $rule);
            $this->manager->persist($rule);
        }
        $this->manager->flush();
    }

    protected function createProvisions(array $provisions)
    {
        foreach ($provisions as $key => $provision) {
            $provision = $this->createProvision($provision['type'], $provision['config']);
            $this->setReference('Sylius.AffiliateProvision.' . $key, $provision);
            $this->manager->persist($provision);
        }
        $this->manager->flush();
    }

    protected function createGoals(array $goals)
    {
        foreach ($goals as $key => $goal) {
            $goal = $this->createGoal(
                $goal['name'],
                $goal['desc'],
                $goal['rules'],
                $goal['provisions']
            );
            $this->setReference('Sylius.AffiliateGoal.' . $key, $goal);
            $this->manager->persist($goal);
        }
        $this->manager->flush();
    }

    protected function createTransactions()
    {
        for ($i = 2; $i <= 15; $i++) {
            $referralCount = rand(0,3);
            $customer = $this->getReference('Sylius.Customer-' . $i);
            $affiliate = $customer->getAffiliate();

            for ($y = 0; $y < $referralCount; $y++) {
                $orderNum = rand(1, 50);
                $goal = $this->getReference('Sylius.AffiliateGoal.' . rand(0, 2));

                $this->get('sylius.affiliate_provision_applicator')->apply($this->getReference('Sylius.Order-' . $orderNum), $affiliate, $goal);
                $this->manager->flush();
            }
        }
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
        $rule = $this->get('sylius.factory.affiliate_rule')->createNew();
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
        $provision = $this->get('sylius.factory.affiliate_provision')->createNew();
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
     * @return AffiliateGoalInterface
     */
    protected function createGoal($name, $description, array $rules, array $provisions)
    {
        /** @var $goal AffiliateGoalInterface */
        $goal = $this->get('sylius.factory.affiliate_goal')->createNew();
        $goal->setName($name);
        $goal->setDescription($description);
        $goal->addChannel($this->getReference('Sylius.Channel.DEFAULT'));

        foreach ($rules as $rule) {
            $goal->addRule($rule);
        }
        foreach ($provisions as $provision) {
            $goal->addProvision($provision);
        }

        $this->setReference('Sylius.AffiliateGoal.'.$name, $goal);

        return $goal;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 70;
    }
}
