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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

abstract class AbstractImporter implements ImporterInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @param ObjectManager       $manager
     * @param FactoryInterface $factory
     * @param array               $options
     */
    public function __construct(ObjectManager $manager, FactoryInterface $factory, array $options = [])
    {
        $this->manager = $manager;
        $this->factory = $factory;

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
        if (!empty($managedCurrencies) && in_array($code, $managedCurrencies)) {
            foreach ($managedCurrencies as $currency) {
                if ($code === $currency->getCode()) {
                    $currency->setExchangeRate($rate);

                    $this->manager->persist($currency);

                    return;
                }
            }
        } else {
            /** @var $currency CurrencyInterface */
            $currency = $this->factory->createNew();
            $currency->setCode($code);
            $currency->setExchangeRate($rate);

            $this->manager->persist($currency);
        }
    }
}
