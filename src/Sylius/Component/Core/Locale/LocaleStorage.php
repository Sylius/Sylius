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

namespace Sylius\Component\Core\Locale;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Storage\StorageInterface;

final class LocaleStorage implements LocaleStorageInterface
{
    /** @var StorageInterface */
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function set(ChannelInterface $channel, string $localeCode): void
    {
        $this->storage->set($this->provideKey($channel), $localeCode);
    }

    public function get(ChannelInterface $channel): string
    {
        $localeCode = $this->storage->get($this->provideKey($channel));
        if (null === $localeCode) {
            throw new LocaleNotFoundException('No locale is set for current channel!');
        }

        return $localeCode;
    }

    private function provideKey(ChannelInterface $channel): string
    {
        return '_locale_' . $channel->getCode();
    }
}
