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

class MoneyContext extends DefaultContext
{
    /**
     * @Given /^there are following currencies configured:$/
     */
    public function thereAreCurrencies(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('currency');

        foreach ($repository->findAll() as $currency) {
            $manager->remove($currency);
        }

        $manager->flush();
        $manager->clear();

        foreach ($table->getHash() as $data) {
            $this->thereIsCurrency($data['code'], $data['exchange rate'], 'yes' === $data['enabled'], false);
        }

        $manager->flush();
    }

    /**
     * @Given /^I created currency "([^""]*)"$/
     */
    public function thereIsCurrency($code, $rate = 1, $enabled = true, $flush = true)
    {
        $repository = $this->getRepository('currency');

        $currency = $repository->createNew();
        $currency->setCode($code);
        $currency->setExchangeRate($rate);
        $currency->setEnabled($enabled);

        $manager = $this->getEntityManager();
        $manager->persist($currency);

        if ($flush) {
            $manager->flush();
        }

        return $currency;
    }

    /**
     * @Given /^there is default currency configured$/
     */
    public function setupDefaultCurrency()
    {
        $this->thereIsCurrency('EUR');
    }
}
