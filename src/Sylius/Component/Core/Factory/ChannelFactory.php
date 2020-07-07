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

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class ChannelFactory implements ChannelFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    /** @var string */
    private $defaultCalculationStrategy;

    public function __construct(FactoryInterface $decoratedFactory, string $defaultCalculationStrategy)
    {
        $this->decoratedFactory = $decoratedFactory;
        $this->defaultCalculationStrategy = $defaultCalculationStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(): ChannelInterface
    {
        /** @var \Sylius\Component\Core\Model\ChannelInterface $channel */
        $channel = $this->decoratedFactory->createNew();
        $channel->setTaxCalculationStrategy($this->defaultCalculationStrategy);

        return $channel;
    }

    /**
     * {@inheritdoc}
     */
    public function createNamed(string $name): ChannelInterface
    {
        $channel = $this->createNew();
        $channel->setName($name);

        return $channel;
    }
}
