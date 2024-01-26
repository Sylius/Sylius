<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Factory;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @template T of ResourceInterface
 *
 * @extends FactoryInterface<T>
 */
interface OrderFactoryInterface extends FactoryInterface
{
    public function createNewCart(
        ChannelInterface $channel,
        ?CustomerInterface $customer,
        string $localeCode,
        ?string $tokenValue = null,
    ): OrderInterface;
}
