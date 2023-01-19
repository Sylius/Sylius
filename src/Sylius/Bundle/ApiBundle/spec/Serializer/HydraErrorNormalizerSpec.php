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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class HydraErrorNormalizerSpec extends ObjectBehavior
{
    function it_decorates_normalize_method(NormalizerInterface $normalizer, RequestStack $requestStack): void
    {
        $this->beConstructedWith($normalizer, $requestStack, '/api/v2');

        $normalizer->normalize('data', 'format', ['context'])->shouldBeCalled();

        $this->normalize('data', 'format', ['context']);
    }

    function it_doesnt_support_normalization_when_path_doesnt_start_with_new_api_route(
        NormalizerInterface $normalizer,
        RequestStack $requestStack,
        Request $request,
    ): void {
        $this->beConstructedWith($normalizer, $requestStack, '/api/v2');

        $request->getPathInfo()->willReturn('/api/v1/resource');

        if (method_exists(RequestStack::class, 'getMainRequest')) {
            $requestStack->getMainRequest()->willReturn($request);
        } else {
            /** @phpstan-ignore-next-line */
            $requestStack->getMasterRequest()->willReturn($request);
        }

        $normalizer->supportsNormalization('data', 'format')->shouldNotBeCalled();

        $this->supportsNormalization('data', 'format');
    }

    function it_calls_decorated_support_normalize_method_when_path_starts_with_new_api_route(
        NormalizerInterface $normalizer,
        RequestStack $requestStack,
        Request $request,
    ): void {
        $this->beConstructedWith($normalizer, $requestStack, '/api/v2');

        $request->getPathInfo()->willReturn('/api/v2/resource');

        if (method_exists(RequestStack::class, 'getMainRequest')) {
            $requestStack->getMainRequest()->willReturn($request);
        } else {
            /** @phpstan-ignore-next-line */
            $requestStack->getMasterRequest()->willReturn($request);
        }

        $normalizer->supportsNormalization('data', 'format')->shouldBeCalled();

        $this->supportsNormalization('data', 'format');
    }

    function it_decorates_has_cacheable_supports_method(
        NormalizerInterface $normalizer,
        RequestStack $requestStack,
    ): void {
        $normalizer->implement(CacheableSupportsMethodInterface::class);

        $this->beConstructedWith($normalizer, $requestStack, '/api/v2');

        $normalizer->hasCacheableSupportsMethod()->shouldBeCalled();

        $this->hasCacheableSupportsMethod();
    }

    function it_doesnt_support_normalization_when_no_request_is_available(
        ContextAwareNormalizerInterface $normalizer,
        RequestStack $requestStack,
    ): void {
        $this->beConstructedWith($normalizer, $requestStack, '/api/v2');

        if (method_exists(RequestStack::class, 'getMainRequest')) {
            $requestStack->getMainRequest()->willReturn(null);
        } else {
            $requestStack->getMasterRequest()->willReturn(null);
        }

        $normalizer->supportsNormalization('data', 'format')->shouldNotBeCalled();

        $this->supportsNormalization('data', 'format');
    }
}
