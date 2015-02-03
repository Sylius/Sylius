<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Channel;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface as BaseChannelContextInterface;

/**
 * Interface for service defining the currently used channel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelContextInterface extends BaseChannelContextInterface
{
    /**
     * {@inheritdoc}
     *
     * @return ChannelInterface
     */
    public function getChannel();
}
