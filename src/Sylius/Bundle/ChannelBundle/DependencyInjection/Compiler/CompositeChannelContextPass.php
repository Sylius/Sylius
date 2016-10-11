<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle\DependencyInjection\Compiler;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CompositeChannelContextPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'sylius.channel_context',
            'sylius.channel_context.composite',
            'sylius.channel_context',
            'addContext'
        );
    }
}
