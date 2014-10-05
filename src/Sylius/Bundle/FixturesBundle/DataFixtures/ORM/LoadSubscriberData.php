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

/**
 * Default subscriber fixtures.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class LoadSubscriberData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $subscriberRepository = $this->getSubscriberRepository();

        for ($i = 0; $i < 5; $i++) {
            $subscriber = $subscriberRepository->createNew();

            $subscriber->setEmail($this->faker->email());
            $subscriber->addSubscriptionList($this->getReference('Sylius.SubscriptionList.' . rand(0, 4)));

            $manager->persist($subscriber);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
