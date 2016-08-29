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
use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Locale\Handler\RequestBasedHandlerInterface;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Core\SyliusLocaleEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class LocaleChangeHandler implements RequestBasedHandlerInterface
{
    /**
     * @var LocaleStorageInterface
     */
    private $localeStorage;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param LocaleStorageInterface $localeStorage
     * @param ChannelContextInterface $channelContext
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        LocaleStorageInterface $localeStorage,
        ChannelContextInterface $channelContext,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->localeStorage = $localeStorage;
        $this->channelContext = $channelContext;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request)
    {
        $localeCode = $request->get('code');
        if (null === $localeCode) {
            throw new HandleException(self::class, 'request does not have the locale code');
        }

        try {
            $this->localeStorage->set($this->channelContext->getChannel(), $localeCode);
        } catch (ChannelNotFoundException $exception) {
            throw new HandleException(self::class, 'sylius cannot found the channel', $exception);
        }

        $this->eventDispatcher->dispatch(SyliusLocaleEvents::CODE_CHANGED, new GenericEvent($request));
    }
}
