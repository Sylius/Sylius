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

namespace Sylius\Component\Channel\Context\RequestBased;

use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

interface RequestResolverInterface
{
    /**
     * @param Request $request
     *
     * @return ChannelInterface|null
     */
    public function findChannel(Request $request): ?ChannelInterface;
}
