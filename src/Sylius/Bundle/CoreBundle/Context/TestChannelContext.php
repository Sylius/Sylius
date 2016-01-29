<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Context;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class TestChannelContext
{
    /**
     * @var string
     */
    private $currentHostname = null;

    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var ChannelContextInterface $channelContext
     */
    public function __construct($channelContext)
    {
        $this->channelContext = $channelContext;
    }

    /**
     * @param string $hostname
     */
    public function setCurrentHostname($hostname)
    {
        $this->currentHostname = $hostname;

        $this->channelContext->setLatestHostname($hostname);
    }

    /**
     * @return ChannelInterface
     */
    public function getChannel()
    {
        if ($this->currentHostname) {
            $this->channelContext->setLatestHostname($this->currentHostname);

            return $this->channelContext->getChannel();
        }

        return $this->channelContext->getChannel();
    }
}
