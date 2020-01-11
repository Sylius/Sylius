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

namespace Sylius\Component\Core\Currency;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;

interface CurrencyStorageInterface
{
    public function set(ChannelInterface $channel, string $currencyCode): void;

    /**
     * @throws CurrencyNotFoundException
     */
    public function get(ChannelInterface $channel): ?string;
}
