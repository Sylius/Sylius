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
 * Default locale fixtures.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LoadLocaleData extends DataFixture
{
    protected $locales = array(
        'en_US',
        'en_GB',
        'de_DE',
    );

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $localeRepository = $this->getLocaleRepository();

        foreach ($this->locales as $code) {
            $locale = $localeRepository->createNew();

            $locale->setCode($code);
            $locale->setEnabled(true);

            $this->setReference('Sylius.Locale.'.$code, $locale);

            $manager->persist($locale);
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
