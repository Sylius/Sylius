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
use Zend\Stdlib\PriorityQueue;

final class CompositeRequestResolver implements RequestResolverInterface
{
    /**
     * @var PriorityQueue|RequestResolverInterface[]
     */
    private $requestResolvers;

    public function __construct()
    {
        $this->requestResolvers = new PriorityQueue();
    }

    /**
     * @param RequestResolverInterface $requestResolver
     * @param int $priority
     */
    public function addResolver(RequestResolverInterface $requestResolver, int $priority = 0): void
    {
        $this->requestResolvers->insert($requestResolver, $priority);
    }

    /**
     * {@inheritdoc}
     */
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
