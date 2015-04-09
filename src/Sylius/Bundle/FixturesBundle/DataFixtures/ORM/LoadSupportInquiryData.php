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
class LoadSupportInquiryData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $repository = $this->getSupportInquiryRepository();

        for ($i = 0; $i < 20; $i++) {
            $inquiry = $repository->createNew();
            $inquiry->setFirstName($this->faker->firstName());
            $inquiry->setLastName($this->faker->lastName());
            $inquiry->setEmail($this->faker->email());
            $inquiry->setMessage($this->faker->paragraph());
            $inquiry->setTopic($this->getReference('Sylius.SupportTopic.' . rand(0, 4)));

            $manager->persist($inquiry);
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
