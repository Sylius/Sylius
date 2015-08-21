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
 * Default support category fixtures.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class LoadSupportCategoryData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $supportCategoryRepository = $this->getSupportCategoryRepository();

        for ($i = 0; $i < 5; $i++) {
            $supportCategory = $supportCategoryRepository->createNew();
            $supportCategory->setCurrentLocale($this->defaultLocale);
            $supportCategory->setFallbackLocale($this->defaultLocale);

            $supportCategory->setTitle($this->faker->sentence());

            $manager->persist($supportCategory);

            $this->setReference('Sylius.SupportCategory.'.$i, $supportCategory);
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
