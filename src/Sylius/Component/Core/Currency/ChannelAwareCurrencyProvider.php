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
use Sylius\Component\Currency\Provider\CurrencyProvider;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Currency provider, which returns currencies enabled for this channel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Fernando Caraballo Ortiz <caraballo.ortiz@gmail.com>
 */
class ChannelAwareCurrencyProvider extends CurrencyProvider
{
    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @param ChannelContextInterface $channelContext
     * @param RepositoryInterface $currencyRepository
     */
    public function __construct(ChannelContextInterface $channelContext, RepositoryInterface $currencyRepository)
    {
        parent::__construct($currencyRepository);

        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableCurrencies()
    {
        $currentChannel = $this->channelContext->getChannel();

        return $currentChannel->getCurrencies()->filter(function (CurrencyInterface $currency) {
            return $currency->isEnabled();
        });
    }
}
