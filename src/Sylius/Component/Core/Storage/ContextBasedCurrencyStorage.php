<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Storage;

use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ContextBasedCurrencyStorage implements CurrencyStorageInterface
{
    /**
     * @var CurrencyStorageInterface
     */
    private $currencyStorage;

    /**
     * @var ShopperContextInterface
     */
    private $shopperContext;

    /**
     * @param CurrencyStorageInterface $currencyStorage
     * @param ShopperContextInterface $shopperContext
     */
    public function __construct(CurrencyStorageInterface $currencyStorage, ShopperContextInterface $shopperContext)
    {
        $this->currencyStorage = $currencyStorage;
        $this->shopperContext = $shopperContext;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ChannelNotFoundException
     */
    public function get(ChannelInterface $channel = null)
    {
        if (null === $channel) {
            $channel = $this->shopperContext->getChannel();
        }

        try {
            return $this->currencyStorage->get($channel);
        } catch (CurrencyNotFoundException $exception) {
            return $this->shopperContext->getCurrencyCode();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws ChannelNotFoundException
     */
    public function set(ChannelInterface $channel = null, $currencyCode)
    {
        if (null === $channel) {
            $channel = $this->shopperContext->getChannel();
        }

        $this->currencyStorage->set($channel, $currencyCode);
    }
}
