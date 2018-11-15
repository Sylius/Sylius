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

namespace Sylius\Component\Core\Locale\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Locale\LocaleStorageInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

final class StorageBasedLocaleContext implements LocaleContextInterface
{
    /** @var ChannelContextInterface */
    private $channelContext;

    /** @var LocaleStorageInterface */
    private $localeStorage;

    /** @var LocaleProviderInterface */
    private $localeProvider;

    public function __construct(
        ChannelContextInterface $channelContext,
        LocaleStorageInterface $localeStorage,
        LocaleProviderInterface $localeProvider
    ) {
        $this->channelContext = $channelContext;
        $this->localeStorage = $localeStorage;
        $this->localeProvider = $localeProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode(): string
    {
        $availableLocalesCodes = $this->localeProvider->getAvailableLocalesCodes();

        try {
            $localeCode = $this->localeStorage->get($this->channelContext->getChannel());
        } catch (ChannelNotFoundException $exception) {
            throw new LocaleNotFoundException(null, $exception);
        }

        if (!in_array($localeCode, $availableLocalesCodes, true)) {
            throw LocaleNotFoundException::notAvailable($localeCode, $availableLocalesCodes);
        }

        return $localeCode;
    }
}
