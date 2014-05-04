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
use Sylius\Component\Core\Model\LocaleInterface;

/**
 * Locale fixtures.
 */
class LoadLocalesData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createLocale('pl', 'PLN'));

        $manager->persist($this->createLocale('en', 'GBP'));

        $manager->persist($this->createLocale('en', 'USD'));

        $manager->persist($this->createLocale('en', 'EUR'));
        $manager->persist($this->createLocale('de', 'EUR', false));

        $manager->flush();
    }

    private function createLocale($code, $currency, $enabled = true)
    {
        /* @var $locale LocaleInterface */
        $locale = $this->getLocaleRepository()->createNew();
        $locale->setCode($code);
        $locale->setCurrency($currency);
        $locale->setEnabled($enabled);

        return $locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
