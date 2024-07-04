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

namespace spec\Sylius\Bundle\AdminBundle\Twig\ErrorTemplateFinder;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AdminBundle\SectionResolver\AdminSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\UiBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

final class ErrorTemplateFinderSpec extends ObjectBehavior
{
    private const TEMPLATE_PREFIX = '@SyliusAdmin/errors';

    function let(
        SectionProviderInterface $sectionProvider,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Environment $twig,
    ): void {
        $this->beConstructedWith($sectionProvider, $tokenStorage, $requestStack, $twig);
    }

    function it_implements_error_template_finder_interface(): void
    {
        $this->shouldImplement(ErrorTemplateFinderInterface::class);
    }

    function it_does_not_find_template_for_other_sections_than_admin(
        SectionProviderInterface $sectionProvider,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Environment $twig,
        SectionInterface $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);

        $tokenStorage->getToken()->shouldNotBeCalled();
        $requestStack->getMainRequest()->shouldNotBeCalled();
        $twig->getLoader()->shouldNotBeCalled();

        $this->findTemplate(404)->shouldReturn(null);
    }

    function it_does_not_find_template_when_there_is_no_token_and_no_main_request_in_admin_section(
        SectionProviderInterface $sectionProvider,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Environment $twig,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminSection());
        $tokenStorage->getToken()->willReturn(null);

        $requestStack->getMainRequest()->willReturn(null);

        $twig->getLoader()->shouldNotBeCalled();

        $this->findTemplate(404)->shouldReturn(null);
    }

    function it_does_not_find_template_when_there_is_no_token_user_and_no_main_request_in_admin_section(
        SectionProviderInterface $sectionProvider,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Environment $twig,
        TokenInterface $token,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminSection());

        $token->getUser()->willReturn(null);
        $tokenStorage->getToken()->willReturn($token);
        $requestStack->getMainRequest()->willReturn(null);

        $twig->getLoader()->shouldNotBeCalled();

        $this->findTemplate(404)->shouldReturn(null);
    }

    function it_does_not_find_template_when_the_token_user_is_not_an_admin_and_there_is_no_main_request_in_admin_section(
        SectionProviderInterface $sectionProvider,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Environment $twig,
        TokenInterface $token,
        UserInterface $user,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminSection());

        $token->getUser()->willReturn($user);
        $tokenStorage->getToken()->willReturn($token);
        $requestStack->getMainRequest()->willReturn(null);

        $twig->getLoader()->shouldNotBeCalled();

        $this->findTemplate(404)->shouldReturn(null);
    }

    function it_does_not_find_template_when_there_is_no_token_and_no_admin_in_session_in_admin_section(
        SectionProviderInterface $sectionProvider,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Environment $twig,
        Request $request,
        SessionInterface $session,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminSection());
        $tokenStorage->getToken()->willReturn(null);

        $session->get('_security_admin')->willReturn(null);
        $request->getSession()->willReturn($session);
        $requestStack->getMainRequest()->willReturn($request);

        $twig->getLoader()->shouldNotBeCalled();

        $this->findTemplate(404)->shouldReturn(null);
    }

    function it_finds_template_for_admin_from_token(
        SectionProviderInterface $sectionProvider,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Environment $twig,
        LoaderInterface $loader,
        TokenInterface $token,
        AdminUserInterface $adminUser,
    ): void {
        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';

        $sectionProvider->getSection()->willReturn(new AdminSection());

        $token->getUser()->willReturn($adminUser);
        $tokenStorage->getToken()->willReturn($token);
        $requestStack->getMainRequest()->shouldNotBeCalled();

        $twig->getLoader()->willReturn($loader);
        $loader->exists($templateName)->shouldBeCalled()->willReturn(true);

        $this->findTemplate(404)->shouldReturn($templateName);
    }

    function it_finds_template_for_admin_from_session(
        SectionProviderInterface $sectionProvider,
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        Environment $twig,
        LoaderInterface $loader,
        Request $request,
        SessionInterface $session,
    ): void {
        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';

        $sectionProvider->getSection()->willReturn(new AdminSection());

        $tokenStorage->getToken()->willReturn(null);

        $session->get('_security_admin')->willReturn('serialized_token');
        $request->getSession()->willReturn($session);
        $requestStack->getMainRequest()->willReturn($request);

        $twig->getLoader()->willReturn($loader);
        $loader->exists($templateName)->shouldBeCalled()->willReturn(true);

        $this->findTemplate(404)->shouldReturn($templateName);
    }

    function it_returns_null_if_neither_template_can_be_found(
        SectionProviderInterface $sectionProvider,
        TokenStorageInterface $tokenStorage,
        Environment $twig,
        LoaderInterface $loader,
        TokenInterface $token,
        AdminUserInterface $adminUser,
    ): void {
        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';
        $fallbackTemplateName = self::TEMPLATE_PREFIX . '/error.html.twig';

        $sectionProvider->getSection()->willReturn(new AdminSection());

        $token->getUser()->willReturn($adminUser);
        $tokenStorage->getToken()->willReturn($token);

        $twig->getLoader()->willReturn($loader);
        $loader->exists($templateName)->shouldBeCalled()->willReturn(false);
        $loader->exists($fallbackTemplateName)->shouldBeCalled()->willReturn(false);

        $this->findTemplate(404)->shouldReturn(null);
    }

    function it_finds_fallback_template_for_admin(
        SectionProviderInterface $sectionProvider,
        TokenStorageInterface $tokenStorage,
        Environment $twig,
        LoaderInterface $loader,
        TokenInterface $token,
        AdminUserInterface $adminUser,
    ): void {
        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';
        $fallbackTemplateName = self::TEMPLATE_PREFIX . '/error.html.twig';

        $sectionProvider->getSection()->willReturn(new AdminSection());

        $token->getUser()->willReturn($adminUser);
        $tokenStorage->getToken()->willReturn($token);

        $twig->getLoader()->willReturn($loader);
        $loader->exists($templateName)->shouldBeCalled()->willReturn(false);
        $loader->exists($fallbackTemplateName)->shouldBeCalled()->willReturn(true);

        $this->findTemplate(404)->shouldReturn($fallbackTemplateName);
    }
}
