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

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Storage\StorageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class LocaleStorage implements LocaleStorageInterface
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @param StorageInterface $storage
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function set(ChannelInterface $channel, $localeCode)
    {
        $this->storage->setData($this->provideKey($channel), $localeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function get(ChannelInterface $channel)
    {
        $localeCode = $this->storage->getData($this->provideKey($channel));
        if (null === $localeCode) {
            throw new LocaleNotFoundException('No locale is set for current channel!');
        }

        return $localeCode;
    }

    /**
     * {@inheritdoc}
     */
    private function provideKey(ChannelInterface $channel)
    {
        return '_locale_' . $channel->getCode();
    }
}
