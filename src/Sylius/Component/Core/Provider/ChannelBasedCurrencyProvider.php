<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Provider;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelBasedCurrencyProvider implements CurrencyProviderInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @param ChannelContextInterface $channelContext
     */
    public function __construct(ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableCurrenciesCodes()
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $channel
                ->getCurrencies()
                ->filter(function (CurrencyInterface $currency) {
                    return $currency->isEnabled();
                })
                ->map(function (CurrencyInterface $currency) {
                    return $currency->getCode();
                })
                ->toArray()
            ;
        } catch (ChannelNotFoundException $exception) {
            throw new CurrencyNotFoundException(null, $exception);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCurrencyCode()
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $channel->getDefaultCurrency()->getCode();
        } catch (ChannelNotFoundException $exception) {
            throw new CurrencyNotFoundException(null, $exception);
        }
    }
}
