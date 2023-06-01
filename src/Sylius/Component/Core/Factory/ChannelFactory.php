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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

/**
 * @template T of ChannelInterface
 *
 * @implements ChannelFactoryInterface<T>
 */
final class ChannelFactory implements ChannelFactoryInterface
{
    /**
     * @param FactoryInterface<T> $decoratedFactory
     * @param FactoryInterface<ChannelPriceHistoryConfigInterface>|null $channelPriceHistoryConfigFactory
     */
    public function __construct(
        private FactoryInterface $decoratedFactory,
        private string $defaultCalculationStrategy,
        private ?FactoryInterface $channelPriceHistoryConfigFactory = null,
    ) {
        if (null === $this->channelPriceHistoryConfigFactory) {
            @trigger_error(sprintf('Not passing a $channelPriceHistoryConfigFactory to %s constructor is deprecated since Sylius 1.13 and will be prohibited in Sylius 2.0.', self::class), \E_USER_DEPRECATED);
        }
    }

    /** @inheritdoc */
    public function createNew(): ChannelInterface
    {
        $channel = $this->decoratedFactory->createNew();
        $channel->setTaxCalculationStrategy($this->defaultCalculationStrategy);

        if (null !== $this->channelPriceHistoryConfigFactory) {
            /** @var ChannelPriceHistoryConfigInterface $channelPriceHistoryConfig */
            $channelPriceHistoryConfig = $this->channelPriceHistoryConfigFactory->createNew();
            $channel->setChannelPriceHistoryConfig($channelPriceHistoryConfig);
        }

        return $channel;
    }

    public function createNamed(string $name): ChannelInterface
    {
        $channel = $this->createNew();
        $channel->setName($name);
        Assert::isInstanceOf($channel, ChannelInterface::class);

        return $channel;
    }
}
