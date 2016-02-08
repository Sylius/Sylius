<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ChannelBundle\Development;

use Sylius\Component\Channel\Context\RequestBased\RequestResolverInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FakeRequestResolver implements RequestResolverInterface
{
    /**
     * @var RequestResolverInterface
     */
    private $decoratedRequestResolver;

    /**
     * @var FakeHostnameProviderInterface
     */
    private $fakeHostnameProvider;

    /**
     * @param RequestResolverInterface $decoratedRequestResolver
     * @param FakeHostnameProviderInterface $fakeHostnameProvider
     */
    public function __construct(
        RequestResolverInterface $decoratedRequestResolver,
        FakeHostnameProviderInterface $fakeHostnameProvider
    ) {
        $this->decoratedRequestResolver = $decoratedRequestResolver;
        $this->fakeHostnameProvider = $fakeHostnameProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function findChannel(Request $request)
    {
        $fakeHostname = $this->fakeHostnameProvider->getHostname($request);

        if (null !== $fakeHostname) {
            $request = clone $request;
            $request->headers->set('HOST', $fakeHostname);
        }

        return $this->decoratedRequestResolver->findChannel($request);
    }
}
