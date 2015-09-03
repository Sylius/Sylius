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
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class LoadSupportTicketData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $supportTicketRepository = $this->getSupportTicketRepository();

        for ($i = 0; $i < 20; $i++) {
            $supportTicket = $supportTicketRepository->createNew();

            $supportTicket->setFirstName($this->faker->firstName());
            $supportTicket->setLastName($this->faker->lastName());
            $supportTicket->setEmail($this->faker->email());
            $supportTicket->setMessage($this->faker->paragraph());
            $supportTicket->setCategory($this->getReference('Sylius.SupportCategory.'.rand(0, 4)));

            $manager->persist($supportTicket);
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
