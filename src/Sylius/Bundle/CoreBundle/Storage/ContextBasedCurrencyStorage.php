<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Storage;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Currency\CurrencyStorageInterface;
use Sylius\Component\Core\SyliusChannelEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param CurrencyStorageInterface $currencyStorage
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(CurrencyStorageInterface $currencyStorage, EventDispatcherInterface $eventDispatcher)
    {
        $this->currencyStorage = $currencyStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function get(ChannelInterface $channel = null)
    {
        $this->currencyStorage->get($channel);
    }

    /**
     * {@inheritdoc}
     */
    public function set(ChannelInterface $channel = null, $currencyCode)
    {
        $this->currencyStorage->set($channel, $currencyCode);

        $this->eventDispatcher->dispatch(SyliusChannelEvents::CURRENCY_CHANGE, new GenericEvent());
    }
}
