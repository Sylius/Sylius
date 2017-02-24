<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Currency;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Storage\StorageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CurrencyStorage implements CurrencyStorageInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function set(ChannelInterface $channel, $currencyCode)
    {
        if ($this->isBaseCurrency($currencyCode, $channel) || !$this->isAvailableCurrency($currencyCode, $channel)) {
            $this->storage->remove($this->provideKey($channel));

            return;
        }

        $this->storage->set($this->provideKey($channel), $currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function get(ChannelInterface $channel)
    {
        return $this->storage->get($this->provideKey($channel));
    }

    /**
     * {@inheritdoc}
     */
    private function provideKey(ChannelInterface $channel)
    {
        return '_currency_' . $channel->getCode();
    }

    /**
     * @param string$currencyCode
     * @param ChannelInterface $channel
     *
     * @return bool
     */
    private function isBaseCurrency($currencyCode, ChannelInterface $channel)
    {
        return $currencyCode === $channel->getBaseCurrency()->getCode();
    }

    /**
     * @param string $currencyCode
     * @param ChannelInterface $channel
     *
     * @return bool
     */
    private function isAvailableCurrency($currencyCode, ChannelInterface $channel)
    {
        $availableCurrencies = array_map(
            function (CurrencyInterface $currency) {
                return $currency->getCode();
            },
            $channel->getCurrencies()->toArray()
        );

        return in_array($currencyCode, $availableCurrencies, true);
    }
}
