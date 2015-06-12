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
 * Default contact request fixtures.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class LoadContactRequestData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $contactRequestRepository = $this->getContactRequestRepository();

        for ($i = 0; $i < 20; $i++) {
            $contactRequest = $contactRequestRepository->createNew();

            $contactRequest->setFirstName($this->faker->firstName());
            $contactRequest->setLastName($this->faker->lastName());
            $contactRequest->setEmail($this->faker->email());
            $contactRequest->setMessage($this->faker->paragraph());
            $contactRequest->setTopic($this->getReference('Sylius.ContactTopic.'.rand(0, 4)));

            $manager->persist($contactRequest);
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
