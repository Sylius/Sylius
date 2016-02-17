<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Services;

use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class DefaultChannelFactory implements DefaultChannelFactoryInterface
{
    const DEFAULT_CHANNEL_NAME = 'Default';
    const DEFAULT_CHANNEL_CODE = 'DEFAULT';

    /**
     * @var ChannelFactoryInterface
     */
    private $channelFactory;

    /**
     * @var RepositoryInterface
     */
    private $channelRepository;

    /**
     * @param ChannelFactoryInterface $channelFactory
     * @param RepositoryInterface $channelRepository
     */
    public function __construct(ChannelFactoryInterface $channelFactory, RepositoryInterface $channelRepository)
    {
        $this->channelFactory = $channelFactory;
        $this->channelRepository = $channelRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $channel = $this->channelFactory->createNamed(self::DEFAULT_CHANNEL_NAME);
        $channel->setCode(self::DEFAULT_CHANNEL_CODE);

        $this->channelRepository->add($channel);

        return ['channel' => $channel];
    }
}
