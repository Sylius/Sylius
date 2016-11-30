<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Handler;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\Currency\Handler\CurrencyChangeHandlerInterface;
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\SyliusCurrencyEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ShopCurrencyChangeHandler implements CurrencyChangeHandlerInterface
{
    /**
     * @var CurrencyStorageInterface
     */
    private $currencyStorage;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param CurrencyStorageInterface $currencyStorage
     * @param ChannelContextInterface $channelContext
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        CurrencyStorageInterface $currencyStorage,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->currencyStorage = $currencyStorage;
        $this->channelContext = $channelContext;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($code)
    {
        try {
            $this->currencyStorage->set($this->channelContext->getChannel(), $code);
        } catch (ChannelNotFoundException $exception) {
            throw new HandleException(self::class, 'Sylius could not find the channel.', $exception);
        }

        $this->eventDispatcher->dispatch(SyliusCurrencyEvents::CODE_CHANGED, new GenericEvent($code));
    }
}
