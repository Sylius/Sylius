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
use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\RewardInterface;
use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Affiliate fixtures.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class LoadAffiliateData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var $admin UserInterface */
        $admin = $this->getReference('Sylius.User-Administrator');

        /** @var $affiliate AffiliateInterface */
        $affiliate = $this->get('sylius.factory.affiliate')->createNew();

        for ($i = 2; $i <= 200; $i += 2) {
            $affiliate->addReferral($this->getReference('Sylius.Customer-'.$i));
        }

        $admin->getCustomer()->setAffiliate($affiliate);

        $this->get('event_dispatcher')->dispatch('sylius.affiliate.pre_create', new GenericEvent($affiliate));

        $manager->persist($admin);
        $manager->persist($affiliate);
        $manager->flush();

        for ($i = 2; $i <= 15; $i++) {
            $customer  = $this->getReference('Sylius.Customer-' . $i);
            $affiliate = $this->get('sylius.factory.affiliate')->createNew();

            $customer->setAffiliate($affiliate);
            $this->get('event_dispatcher')->dispatch('sylius.affiliate.pre_create', new GenericEvent($affiliate));
            $manager->persist($customer);
            $manager->persist($affiliate);
        }

        $manager->flush();
    }

    /**
     * @param \DateTime $date
     * @param int       $type
     * @param int       $amount
     *
     * @return RewardInterface
     */
    protected function createReward(\DateTime $date, $type, $amount)
    {
        /** @var $reward RewardInterface */
        $reward = $this->get('sylius.factory.reward')->createNew();
        $reward->setType($type);
        $reward->setAmount($amount);
        $reward->setCurrency('EUR');
        $reward->setCreatedAt($date);

        return $reward;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 30;
    }
}
