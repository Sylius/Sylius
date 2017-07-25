<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Channel\Context;

use Doctrine\DBAL\Exception\TableNotFoundException;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class SingleChannelContext implements ChannelContextInterface
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        try {
            $channels = $this->channelRepository->findAll();
        } catch (TableNotFoundException $dbalException) {
            throw new ChannelNotFoundException();
        }

        if (1 !== count($channels)) {
            throw new ChannelNotFoundException();
        }

        return current($channels);
    }
}
