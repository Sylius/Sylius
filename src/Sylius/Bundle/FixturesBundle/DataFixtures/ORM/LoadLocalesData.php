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
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LoadLocalesData extends DataFixture
{
    private $locales = array(
        'en' => true,
        'es' => true,
        'de' => true,
        'it' => false,
    );

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $localeRepository = $this->getLocaleRepository();

        $locales = array_merge($this->locales, array($this->defaultLocale => true));

        foreach ($locales as $code => $enabled) {
            $locale = $localeRepository->createNew();
            $locale->setCode($code);
            $locale->setEnabled($enabled);

            $manager->persist($locale);

            $this->setReference('Sylius.Locale.'.$code, $locale);
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
