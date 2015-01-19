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
class LoadLocaleData extends DataFixture
{
    private $locales = array(
        'en_US' => true,
        'en_GB' => true,
        'es_ES' => true,
        'de_DE' => true,
        'it_IT' => false,
        'pl_PL' => true,
    );

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $localeFactory = $this->getLocaleFactory();
        $localeManager = $this->getLocaleManager();

        $locales = array_merge($this->locales, array($this->defaultLocale => true));

        foreach ($locales as $code => $enabled) {
            $locale = $localeFactory->createNew();
            $locale->setCode($code);
            $locale->setEnabled($enabled);

            $localeManager->persist($locale);

            $this->setReference('Sylius.Locale.'.$code, $locale);
        }

        $localeManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }
}
