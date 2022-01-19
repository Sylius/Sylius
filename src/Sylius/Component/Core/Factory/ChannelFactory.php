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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelInterface as CoreChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ChannelFactory implements ChannelFactoryInterface
{
    public function __construct(private FactoryInterface $decoratedFactory, private string $defaultCalculationStrategy)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(): ChannelInterface
    {
        /** @var CoreChannelInterface $channel */
        $channel = $this->decoratedFactory->createNew();
        $channel->setTaxCalculationStrategy($this->defaultCalculationStrategy);

        return $channel;
    }

    /**
     * @return CoreChannelInterface
     */
    public function createNamed(string $name): ChannelInterface
    {
        $channel = $this->createNew();
        $channel->setName($name);

        return $channel;
    }
}
