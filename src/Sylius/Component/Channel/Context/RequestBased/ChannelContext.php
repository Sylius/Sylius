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

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ChannelContext implements ChannelContextInterface
{
    /**
     * @var RequestResolverInterface
     */
    private $requestResolver;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestResolverInterface $requestResolver
     * @param RequestStack $requestStack
     */
    public function __construct(RequestResolverInterface $requestResolver, RequestStack $requestStack)
    {
        $this->requestResolver = $requestResolver;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        try {
            return $this->getChannelForRequest($this->getMasterRequest());
        } catch (\UnexpectedValueException $exception) {
            throw new ChannelNotFoundException($exception);
        }
    }

    /**
     * @param Request $request
     *
     * @return ChannelInterface
     */
    private function getChannelForRequest(Request $request)
    {
        $channel = $this->requestResolver->findChannel($request);

        $this->assertChannelWasFound($channel);

        return $channel;
    }

    /**
     * @return Request
     */
    private function getMasterRequest()
    {
        $masterRequest = $this->requestStack->getMasterRequest();
        if (null === $masterRequest) {
            throw new \UnexpectedValueException('There are not any requests on request stack');
        }

        return $masterRequest;
    }

    /**
     * @param ChannelInterface|null $channel
     */
    private function assertChannelWasFound(ChannelInterface $channel = null)
    {
        if (null === $channel) {
            throw new \UnexpectedValueException('Channel was not found for given request');
        }
    }
}
