<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Intl\Intl;

/**
 * Default exchange rate fixtures.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LoadExchangeRatesData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $exchangeRateRepository = $this->getExchangeRateRepository();

        foreach (Intl::getCurrencyBundle()->getCurrencyNames() as $name => $iso) {
            $exchangeRate = $exchangeRateRepository->createNew();

            $exchangeRate->setCurrency($name);
            $exchangeRate->setRate($this->faker->randomFloat());

            $manager->persist($exchangeRate);
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
