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

namespace Sylius\Component\Channel\Resolver;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface ShippableCountriesResolverInterface
{
    /**
     * @param ChannelInterface $channel
     *
     * @return CountryInterface[]
     */
    public function __invoke(ChannelInterface $channel): array;
}
