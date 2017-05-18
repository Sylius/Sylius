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

use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
interface LocaleStorageInterface
{
    /**
     * @param ChannelInterface $channel
     * @param string $localeCode
     */
    public function set(ChannelInterface $channel, $localeCode);

    /**
     * @param ChannelInterface $channel
     *
     * @return string Locale code
     *
     * @throws ChannelNotFoundException
     */
    public function get(ChannelInterface $channel);
}
