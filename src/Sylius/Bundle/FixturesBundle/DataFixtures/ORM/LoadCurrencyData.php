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
 * Default currency fixtures.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class LoadCurrencyData extends DataFixture
{
    protected $currencies = array(
        'EUR' => 1.00,
        'USD' => 1.30,
        'GBP' => 0.85,
    );

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $currencyRepository = $this->getCurrencyRepository();

        foreach ($this->currencies as $code => $rate) {
            $currency = $currencyRepository->createNew();

            $currency->setCode($code);
            $currency->setExchangeRate($rate);
            $currency->setEnabled(true);

            $this->setReference('Sylius.Currency.'.$code, $currency);

            $manager->persist($currency);
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
