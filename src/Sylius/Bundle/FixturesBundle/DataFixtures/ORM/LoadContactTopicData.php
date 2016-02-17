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
 * Default contact topic fixtures.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class LoadContactTopicData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $contactTopicFactory = $this->getContactTopicFactory();

        for ($i = 0; $i < 5; ++$i) {
            $contactTopic = $contactTopicFactory->createNew();
            $contactTopic->setCurrentLocale($this->defaultLocale);
            $contactTopic->setFallbackLocale($this->defaultLocale);

            $contactTopic->setTitle($this->faker->sentence());

            $manager->persist($contactTopic);

            $this->setReference('Sylius.ContactTopic.'.$i, $contactTopic);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
