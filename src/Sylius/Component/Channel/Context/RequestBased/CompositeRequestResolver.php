<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Channel\Context\RequestBased;

use Symfony\Component\HttpFoundation\Request;
use Zend\Stdlib\PriorityQueue;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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
    public function addResolver(RequestResolverInterface $requestResolver, $priority = 0)
    {
        $this->requestResolvers->insert($requestResolver, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function findChannel(Request $request)
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
