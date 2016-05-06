<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Channel\Context;

use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CachedPerRequestChannelContext implements ChannelContextInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $decoratedChannelContext;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var \SplObjectStorage|ChannelInterface[]
     */
    private $requestToChannelMap;

    /**
     * @var \SplObjectStorage|ChannelNotFoundException[]
     */
    private $requestToExceptionMap;

    /**
     * @param ChannelContextInterface $decoratedChannelContext
     * @param RequestStack $requestStack
     */
    public function __construct(ChannelContextInterface $decoratedChannelContext, RequestStack $requestStack)
    {
        $this->decoratedChannelContext = $decoratedChannelContext;
        $this->requestStack = $requestStack;

        $this->requestToChannelMap = new \SplObjectStorage();
        $this->requestToExceptionMap = new \SplObjectStorage();
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        $objectIdentifier = $this->requestStack->getMasterRequest();

        if (null === $objectIdentifier) {
            return $this->decoratedChannelContext->getChannel();
        }

        if (isset($this->requestToExceptionMap[$objectIdentifier])) {
            throw $this->requestToExceptionMap[$objectIdentifier];
        }

        try {
            if (!isset($this->requestToChannelMap[$objectIdentifier])) {
                $this->requestToChannelMap[$objectIdentifier] = $this->decoratedChannelContext->getChannel();
            }

            return $this->requestToChannelMap[$objectIdentifier];
        } catch (ChannelNotFoundException $exception) {
            $this->requestToExceptionMap[$objectIdentifier] = $exception;

            throw $exception;
        }
    }
}
