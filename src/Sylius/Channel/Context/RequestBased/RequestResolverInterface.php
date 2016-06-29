<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Channel\Context\RequestBased;

use Sylius\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface RequestResolverInterface
{
    /**
     * @param Request $request
     *
     * @return ChannelInterface|null
     */
    public function findChannel(Request $request);
}
