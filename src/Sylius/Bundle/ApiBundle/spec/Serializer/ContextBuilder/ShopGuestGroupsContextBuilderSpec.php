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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\ContextBuilder;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;

final class ShopGuestGroupsContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
    ): void {
        $this->beConstructedWith($decoratedContextBuilder, $sectionProvider, $userContext);
    }

    function it_does_not_add_guest_groups_to_context_if_section_is_not_shop_api(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        AdminApiSection $section,
        Request $request,
    ): void {
        $decoratedContextBuilder->createFromRequest($request, true, [])->willReturn([]);
        $sectionProvider->getSection()->willReturn($section);

        $this->createFromRequest($request, true, [])->shouldReturn([]);

        $userContext->getUser()->shouldNotHaveBeenCalled();
    }

    function it_does_not_add_guest_groups_to_context_if_user_is_not_guest(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShopApiSection $section,
        ShopUserInterface $user,
        Request $request,
    ): void {
        $decoratedContextBuilder->createFromRequest($request, true, [])->willReturn([]);
        $sectionProvider->getSection()->willReturn($section);
        $userContext->getUser()->willReturn($user);

        $this->createFromRequest($request, true, [])->shouldReturn([]);
    }

    function it_does_not_add_guest_groups_to_context_if_context_does_not_have_groups(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShopApiSection $section,
        Request $request,
    ): void {
        $decoratedContextBuilder->createFromRequest($request, true, [])->willReturn([]);
        $sectionProvider->getSection()->willReturn($section);
        $userContext->getUser()->willReturn(null);

        $this->createFromRequest($request, true, [])->shouldReturn([]);
    }

    function it_adds_normalization_guest_groups_to_context(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShopApiSection $section,
        Request $request,
    ): void {
        $decoratedContextBuilder->createFromRequest($request, true, [])->willReturn(['groups' => ['sylius']]);
        $sectionProvider->getSection()->willReturn($section);
        $userContext->getUser()->willReturn(null);

        $this->createFromRequest($request, true, [])->shouldReturn(['groups' => ['sylius', 'sylius:shop:guest:read']]);
    }

    function it_adds_denormalization_guest_groups_to_context(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        SectionProviderInterface $sectionProvider,
        UserContextInterface $userContext,
        ShopApiSection $section,
        Request $request,
    ): void {
        $decoratedContextBuilder->createFromRequest($request, false, [])->willReturn(['groups' => ['sylius']]);
        $sectionProvider->getSection()->willReturn($section);
        $userContext->getUser()->willReturn(null);

        $this->createFromRequest($request, false, [])->shouldReturn(['groups' => ['sylius', 'sylius:shop:guest:write']]);
    }
}
