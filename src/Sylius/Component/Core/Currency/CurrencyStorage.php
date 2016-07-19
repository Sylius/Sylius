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

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Storage\StorageInterface;

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
        $this->storage->setData($this->provideKey($channel), $currencyCode);
    }

    /**
     * {@inheritdoc}
     */
    public function get(ChannelInterface $channel)
    {
        $currencyCode = $this->storage->getData($this->provideKey($channel));
        if (null === $currencyCode) {
            throw new CurrencyNotFoundException('No currency is set for current channel!');
        }

        return $currencyCode;
    }

    /**
     * {@inheritdoc}
     */
    private function provideKey(ChannelInterface $channel)
    {
        return sprintf('_currency_%s', $channel->getCode());
    }
}
