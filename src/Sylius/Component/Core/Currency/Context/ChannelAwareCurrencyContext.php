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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Model\CurrencyInterface;

final class ChannelAwareCurrencyContext implements CurrencyContextInterface
{
    /** @var CurrencyContextInterface */
    private $currencyContext;

    /** @var ChannelContextInterface */
    private $channelContext;

    public function __construct(CurrencyContextInterface $currencyContext, ChannelContextInterface $channelContext)
    {
        $this->currencyContext = $currencyContext;
        $this->channelContext = $channelContext;
    }

    public function getCurrencyCode(): string
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        try {
            $currencyCode = $this->currencyContext->getCurrencyCode();

            if (!$this->isAvailableCurrency($currencyCode, $channel)) {
                return $channel->getBaseCurrency()->getCode();
            }

            return $currencyCode;
        } catch (CurrencyNotFoundException $exception) {
            return $channel->getBaseCurrency()->getCode();
        }
    }

    private function isAvailableCurrency(string $currencyCode, ChannelInterface $channel): bool
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
