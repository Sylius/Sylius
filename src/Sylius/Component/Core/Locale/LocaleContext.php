<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Locale;

use Sylius\Component\Storage\StorageInterface;

/**
 * Locale context per channel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleContext implements LocaleContextInterface
{
    const STORAGE_KEY = '_sylius.locale.%s';

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    public function __construct(StorageInterface $storage, ChannelContextInterface $channelContext)
    {
        $this->channelContext = $channelContext;

        parent::__construct($storage);
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        $channel = $this->channelContext->getChannel();

        return $this->storage->getData($this->getStorageKey($channel->getCode()), $this->defaultLocale);
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $channel = $this->channelContext->getChannel();

        return $this->storage->setData($this->getStorageKey($channel->getCode()), $locale);
    }

    /**
     * Get storage key for channel with given code.
     *
     * @param string $channelCode
     */
    private function getStorageKey($channelCode)
    {
        return sprintf(self::STORAGE_KEY, $channelCode);
    }
}
