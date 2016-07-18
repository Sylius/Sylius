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
final class ChannelAwareCurrencyProvider implements CurrencyProviderInterface
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
    public function getAvailableCurrencies()
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $channel
                ->getCurrencies()
                ->filter(function (CurrencyInterface $currency) {
                    return $currency->isEnabled();
                })
                ->toArray()
            ;
        } catch (ChannelNotFoundException $exception) {
            throw new CurrencyNotFoundException(
                'Available currencies cannot be found because there channel cannot be determined!',
                $exception
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCurrency()
    {
        try {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $channel->getDefaultCurrency();
        } catch (ChannelNotFoundException $exception) {
            throw new CurrencyNotFoundException(null, $exception);
        }
    }
}
