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

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for service defining the currently used channel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ChannelResolverInterface
{
    /**
     * Get currently used channel.
     *
     * @param Request $request
     *
     * @return null|ChannelInterface
     */
    public function resolve(Request $request);
}
