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
            $exchangeRate = isset($data['exchange rate']) ? $data['exchange rate'] : 1;
            $enabled = isset($data['enabled']) ? 'yes' === $data['enabled'] : true;

            $this->thereIsCurrency($data['code'], $exchangeRate, $enabled, false);
        }

        $manager->flush();
    }

    /**
     * @Given /^I created currency "([^""]*)"$/
     * @Given /^there is an enabled currency "([^""]*)"$/
     */
    public function thereIsCurrency($code, $rate = 1, $enabled = true, $flush = true)
    {
        $repository = $this->getRepository('currency');
        $factory = $this->getFactory('currency');

        if (null === $currency = $this->getRepository('currency')->findOneBy(array('code' => $code))) {
            $currency = $factory->createNew();
            $currency->setCode($code);
            $currency->setExchangeRate($rate);
        }

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

    /**
     * @Given /^there is a disabled currency "([^""]*)"$/
     */
    public function thereIsDisabledCurrency($code)
    {
        $this->thereIsCurrency($code, 1, false);
    }
}
