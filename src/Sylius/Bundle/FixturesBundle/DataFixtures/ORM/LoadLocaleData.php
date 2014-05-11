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
use Symfony\Component\Yaml\Yaml;

/**
 * Default locales data.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadLocaleData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $file = __DIR__.'/../../Resources/locales.yml';
        $data = Yaml::parse(file_get_contents($file));
        $localeRepository = $this->getLocaleRepository();

        foreach ($data['locales'] as $code => $enabled) {
            $locale = $localeRepository->createNew();

            $locale->setCode($code);
            $locale->setEnabled($enabled);

            $manager->persist($locale);

            $this->setReference('Sylius.Locale'.$code, $locale);
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
