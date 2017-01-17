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

use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\PrioritizedCompositeServicePass;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CompositeRequestResolverPass extends PrioritizedCompositeServicePass
{
    public function __construct()
    {
        parent::__construct(
            'sylius.context.channel.request_based.resolver',
            'sylius.context.channel.request_based.resolver.composite',
            'sylius.context.channel.request_based.resolver',
            'addResolver'
        );
    }
}
