<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Channel;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class UnresolvedChannelException extends \InvalidArgumentException
{
    public function __construct($hostname)
    {
        parent::__construct(sprintf('Channel for hostname "%s" cannot be resolved.', $hostname));
    }
}