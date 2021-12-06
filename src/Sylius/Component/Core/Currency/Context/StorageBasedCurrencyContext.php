<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Currency\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;

final class StorageBasedCurrencyContext implements CurrencyContextInterface
{
    private ChannelContextInterface $channelContext;

    private CurrencyStorageInterface $currencyStorage;

    public function __construct(ChannelContextInterface $channelContext, CurrencyStorageInterface $currencyStorage)
    {
        $this->channelContext = $channelContext;
        $this->currencyStorage = $currencyStorage;
    }

    public function getCurrencyCode(): string
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $currencyCode = $this->currencyStorage->get($channel);

        if (null === $currencyCode) {
            throw new CurrencyNotFoundException();
        }

        return $currencyCode;
    }
}
