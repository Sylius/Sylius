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
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\SubscriptionInterface;

/**
 * Sample subscription data
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class LoadSubscriptionsData extends DataFixture
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $numUsers = 15;

        for ($i = 0;$i <= $numUsers;$i++) {

            $user = $this->getReference(($i == 0) ? 'Sylius.User-Administrator' : 'Sylius.User-'.$i);

            $numSubscriptions = rand(1, 5);

            for ($s = 0;$s < $numSubscriptions;$s++) {
                $subscription = $this->createSubscription($user);
                $manager->persist($subscription);
            }
        }

        $manager->flush();
    }

    public function createSubscription($user)
    {
        /** @var SubscriptionInterface $subscription */
        $subscription = $this->getSubscriptionRepository()->createNew();

        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->getReference('Sylius.Order-'.rand(1, 50))
            ->getItems()
            ->first()
        ;
        $variant = $orderItem->getVariant();

        $interval = new \DateInterval(
            'P'.$this->faker->randomElement(array(15, 30, 60, 90)).'D'
        );

        $subscription
            ->setUser($user)
            ->setScheduledDate($this->faker->dateTimeBetween('now', '+1 month'))
            ->setProcessedDate($this->faker->dateTimeBetween('now', '+1 month'))
            ->setInterval($interval)
            ->setMaxCycles($this->faker->randomElement(array(null, rand(1, 12))))
            ->setVariant($variant)
            ->setQuantity(rand(1, 5))
        ;

        $orderItem->setSubscription($subscription);

        return $subscription;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 8;
    }
}
