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
 * Default support topic fixtures.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class LoadSupportTopicData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $supportTopicRepository = $this->getSupportTopicRepository();

        for ($i = 0; $i < 5; $i++) {
            $supportTopic = $supportTopicRepository->createNew();
            $supportTopic->setCurrentLocale($this->defaultLocale);
            $supportTopic->setFallbackLocale($this->defaultLocale);

            $supportTopic->setTitle($this->faker->sentence());

            $manager->persist($supportTopic);

            $this->setReference('Sylius.SupportTopic.'.$i, $supportTopic);
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
