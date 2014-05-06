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

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;

/**
 * Currency provider, which returns currencies enabled for this channel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CurrencyProvider implements CurrencyProviderInterface
{
    /**
     * Channel context.
     *
     * @var ChannelContextInterface
     */
    protected $channelContext;

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
        $currentChannel =  $this->channelContext->getChannel();

        return $currentChannel->getCurrencies()->filter(function (CurrencyInterface $currency) {
            return $currency->isEnabled();
        });
    }
}
