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

use Sylius\Component\Core\Model\ChannelInterface;

interface ShippableCountriesResolverInterface
{
    /**
     * @param ChannelInterface|null $channel
     *
     * @return array
     */
    public function getShippableCountries(ChannelInterface $channel = null): array;
}
