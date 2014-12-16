<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Importer;

use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Manager\DomainManagerInterface;

abstract class AbstractImporter implements ImporterInterface
{
    /**
     * @var DomainManagerInterface
     */
    protected $manager;

    public function __construct(DomainManagerInterface $manager, array $options = array())
    {
        $this->manager = $manager;

        $this->configure($options);
    }

    /**
     * @param CurrencyInterface[] $managedCurrencies
     * @param string              $code
     * @param float               $rate
     *
     * @return null|CurrencyInterface
     */
    protected function updateOrCreate(array $managedCurrencies, $code, $rate)
    {
        if (!empty($managedCurrencies) && !in_array($code, $managedCurrencies)) {
            foreach ($managedCurrencies as $currency) {
                if ($code === $currency->getCode()) {
                    $currency->setExchangeRate($rate);

                    $this->manager->update($currency);

                    return;
                }
            }
        } else {
            /** @var $currency CurrencyInterface */
            $currency = $this->manager->createNew();
            $currency->setCode($code);
            $currency->setExchangeRate($rate);

            $this->manager->create($currency);
        }
    }
}
