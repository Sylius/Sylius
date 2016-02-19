<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\ChannelContextSetterInterface;
use Sylius\Component\Core\Model\ChannelInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelContext implements Context
{
    /**
     * @var ChannelContextSetterInterface
     */
    private $channelContextSetter;

    /**
     * @param ChannelContextSetterInterface $channelContextSetter
     */
    public function __construct(ChannelContextSetterInterface $channelContextSetter)
    {
        $this->channelContextSetter = $channelContextSetter;
    }

    /**
     * @When I change my current channel to :channel
     */
    public function iChangeMyCurrentChannelTo(ChannelInterface $channel)
    {
        $this->channelContextSetter->setChannel($channel);
    }
}
