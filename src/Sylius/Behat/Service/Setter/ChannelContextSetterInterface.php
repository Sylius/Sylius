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

namespace Sylius\Behat\Service\Setter;

use Sylius\Component\Channel\Model\ChannelInterface;

interface ChannelContextSetterInterface
{
    /**
     * @param ChannelInterface $channel
     */
    public function setChannel(ChannelInterface $channel);
}
