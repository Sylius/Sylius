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

        $currencies = array(
            'EUR' => 1.00,
            'USD' => 1.30,
            'GBP' => 0.85
        );

        foreach ($currencies as $currency => $rate) {
            $exchangeRate = $exchangeRateRepository->createNew();

            $exchangeRate->setCurrency($currency);
            $exchangeRate->setRate($rate);

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
