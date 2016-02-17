<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CurrencyContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $currencyRepository;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var FactoryInterface
     */
    private $currencyFactory;

    /**
     * @param RepositoryInterface $currencyRepository
     * @param SharedStorageInterface $sharedStorage
     * @param FactoryInterface $currencyFactory
     */
    public function __construct(
        RepositoryInterface $currencyRepository,
        SharedStorageInterface $sharedStorage,
        FactoryInterface $currencyFactory
    ) {
        $this->currencyRepository = $currencyRepository;
        $this->sharedStorage = $sharedStorage;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * @Given default currency is :currencyCode
     */
    public function defaultCurrencyIs($currencyCode)
    {
        $currency = $this->currencyFactory->createNew();
        $currency->setCode($currencyCode);
        $currency->setExchangeRate(1.0);
        $channel = $this->sharedStorage->getCurrentResource('channel');
        $channel->setDefaultCurrency($currency);

        $this->currencyRepository->add($currency);
    }
}
