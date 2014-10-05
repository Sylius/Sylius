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
 * Default subscription list fixtures.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class LoadSubscriptionListData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $subscriptionListRepository = $this->getSubscriptionListRepository();

        for ($i = 0; $i < 5; $i++) {
            $subscriptionList = $subscriptionListRepository->createNew();

            $subscriptionList->setName($this->faker->word);
            $subscriptionList->setDescription($this->faker->sentence());

            $manager->persist($subscriptionList);

            $this->setReference('Sylius.SubscriptionList.' . $i, $subscriptionList);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
