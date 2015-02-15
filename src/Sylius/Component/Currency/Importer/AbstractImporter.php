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
use Sylius\Component\Resource\Repository\RepositoryInterface;

abstract class AbstractImporter implements ImporterInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    public function __construct(ObjectManager $manager, RepositoryInterface $repository, array $options = array())
    {
        $this->manager    = $manager;
        $this->repository = $repository;

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
            $currency = $this->repository->createNew();
            $currency->setCode($code);
            $currency->setExchangeRate($rate);

            $this->manager->persist($currency);
        }
    }
}
