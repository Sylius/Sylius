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

final class CurrencyContext implements CurrencyContextInterface
{
    const STORAGE_KEY = '_sylius_currency';

    /**
     * @var string
     */
    protected $defaultCurrencyCode;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param StorageInterface $storage
     * @param string $defaultCurrencyCode
     */
    public function __construct(StorageInterface $storage, $defaultCurrencyCode)
    {
        $this->storage = $storage;
        $this->defaultCurrencyCode = $defaultCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCurrencyCode()
    {
        return $this->defaultCurrencyCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode()
    {
        return $this->storage->getData(self::STORAGE_KEY, $this->defaultCurrencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrencyCode($currencyCode)
    {
        return $this->storage->setData(self::STORAGE_KEY, $currencyCode);
    }
}
