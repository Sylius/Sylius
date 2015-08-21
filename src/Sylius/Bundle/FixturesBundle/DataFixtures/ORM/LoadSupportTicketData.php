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
 * Default support request fixtures.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class LoadSupportTicketData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $supportRequestRepository = $this->getSupportTicketRepository();

        for ($i = 0; $i < 20; $i++) {
            $supportRequest = $supportRequestRepository->createNew();

            $supportRequest->setFirstName($this->faker->firstName());
            $supportRequest->setLastName($this->faker->lastName());
            $supportRequest->setEmail($this->faker->email());
            $supportRequest->setMessage($this->faker->paragraph());
            $supportRequest->setCategory($this->getReference('Sylius.SupportCategory.'.rand(0, 4)));

            $manager->persist($supportRequest);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 20;
    }
}
