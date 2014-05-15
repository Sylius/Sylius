<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Money\Model\ExchangeRateInterface;

class MoneyContext extends DefaultContext
{
    /**
     * @Given /^there are following exchange rates:$/
     */
    public function thereAreExchangeRates(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsExchangeRate($data['currency'], $data['rate'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created exchange rate "([^""]*)"$/
     */
    public function thereIsExchangeRate($currency, $rate = 1, $flush = true)
    {
        $repository = $this->getRepository('exchange_rate');

        /* @var $exchangeRate ExchangeRateInterface */
        $exchangeRate = $repository->createNew();
        $exchangeRate->setCurrency($currency);
        $exchangeRate->setRate($rate);

        $manager = $this->getEntityManager();
        $manager->persist($exchangeRate);
        if ($flush) {
            $manager->flush();
        }

        return $exchangeRate;
    }
}
