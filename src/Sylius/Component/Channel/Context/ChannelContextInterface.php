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

use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelContextInterface
{
    /**
     * @return ChannelInterface
     *
     * @throws ChannelNotFoundException
     */
    public function getChannel();
}
