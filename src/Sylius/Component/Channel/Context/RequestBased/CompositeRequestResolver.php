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

final class CompositeRequestResolver implements RequestResolverInterface
{
    private iterable $requestResolvers;

    public function __construct(iterable $requestResolvers = [])
    {
        $this->requestResolvers = $requestResolvers instanceof \Traversable ? iterator_to_array($requestResolvers) : $requestResolvers;
    }

    public function findChannel(Request $request): ?ChannelInterface
    {
        foreach ($this->requestResolvers as $requestResolver) {
            $channel = $requestResolver->findChannel($request);

            if (null !== $channel) {
                return $channel;
            }
        }

        return null;
    }
}
