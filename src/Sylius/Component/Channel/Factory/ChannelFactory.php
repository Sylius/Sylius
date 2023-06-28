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

namespace Sylius\Component\Channel\Factory;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of ChannelInterface
 *
 * @implements ChannelFactoryInterface<T>
 */
final class ChannelFactory implements ChannelFactoryInterface
{
    public function __construct(private FactoryInterface $defaultFactory)
    {
    }

    public function createNew(): ChannelInterface
    {
        return $this->defaultFactory->createNew();
    }

    public function createNamed(string $name): ChannelInterface
    {
        /** @var ChannelInterface $channel */
        $channel = $this->defaultFactory->createNew();
        $channel->setName($name);

        return $channel;
    }
}
