<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Context;

use Sylius\Component\Storage\StorageInterface;

class CurrencyContext implements CurrencyContextInterface
{
    /**
     * @var string
     */
    protected $defaultCurrency;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param StorageInterface $storage
     * @param string           $defaultCurrency
     */
    public function __construct(StorageInterface $storage, $defaultCurrency)
    {
        $this->storage = $storage;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCurrency()
    {
        return $this->defaultCurrency;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->storage->getData(self::STORAGE_KEY, $this->defaultCurrency);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        return $this->storage->setData(self::STORAGE_KEY, $currency);
    }
}
