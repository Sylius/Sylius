<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\SectionResolver;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionCannotBeResolvedException;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\UriBasedSectionResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class UriBasedSectionProviderSpec extends ObjectBehavior
{
    function let(
        RequestStack $requestStack,
        UriBasedSectionResolverInterface $firstSectionResolver,
        UriBasedSectionResolverInterface $secondSectionResolver,
    ): void {
        $this->beConstructedWith($requestStack, [$firstSectionResolver, $secondSectionResolver]);
    }

    function it_is_section_resolver(): void
    {
        $this->shouldImplement(SectionProviderInterface::class);
    }

    function it_resolves_first_section_based_on_injected_resolvers(
        RequestStack $requestStack,
        Request $request,
        UriBasedSectionResolverInterface $firstSectionResolver,
        SectionInterface $section,
    ): void {
        $requestStack->getMainRequest()->willReturn($request);

        $request->getPathInfo()->willReturn('/something');

        $firstSectionResolver->getSection('/something')->willReturn($section);

        $this->getSection()->shouldReturn($section);
    }

    function it_resolves_second_section_if_first_will_throw_an_exception(
        RequestStack $requestStack,
        Request $request,
        UriBasedSectionResolverInterface $firstSectionResolver,
        UriBasedSectionResolverInterface $secondSectionResolver,
        SectionInterface $section,
    ): void {
        $requestStack->getMainRequest()->willReturn($request);

        $request->getPathInfo()->willReturn('/something');

        $firstSectionResolver->getSection('/something')->willThrow(new SectionCannotBeResolvedException());
        $secondSectionResolver->getSection('/something')->willReturn($section);

        $this->getSection()->shouldReturn($section);
    }

    function it_return_null_if_master_request_has_not_been_resolved(RequestStack $requestStack): void
    {
        $requestStack->getMainRequest()->willReturn(null);

        $this->getSection()->shouldReturn(null);
    }
}
