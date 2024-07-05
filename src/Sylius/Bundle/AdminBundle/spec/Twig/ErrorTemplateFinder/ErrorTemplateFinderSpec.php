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
use Sylius\Bundle\AdminBundle\Provider\LoggedInUserProviderInterface;
use Sylius\Bundle\AdminBundle\SectionResolver\AdminSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\UiBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinderInterface;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

final class ErrorTemplateFinderSpec extends ObjectBehavior
{
    private const TEMPLATE_PREFIX = '@SyliusAdmin/errors';

    function let(
        SectionProviderInterface $sectionProvider,
        LoggedInUserProviderInterface $loggedInUserProvider,
        Environment $twig,
    ): void {
        $this->beConstructedWith($sectionProvider, $loggedInUserProvider, $twig);
    }

    function it_implements_error_template_finder_interface(): void
    {
        $this->shouldImplement(ErrorTemplateFinderInterface::class);
    }

    function it_does_not_find_template_for_other_sections_than_admin(
        SectionProviderInterface $sectionProvider,
        LoggedInUserProviderInterface $loggedInUserProvider,
        Environment $twig,
        SectionInterface $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);

        $loggedInUserProvider->hasUser()->shouldNotBeCalled();
        $twig->getLoader()->shouldNotBeCalled();

        $this->findTemplate(404)->shouldReturn(null);
    }

    function it_does_not_find_template_when_there_is_no_admin_user(
        SectionProviderInterface $sectionProvider,
        LoggedInUserProviderInterface $loggedInUserProvider,
        Environment $twig,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminSection());

        $loggedInUserProvider->hasUser()->willReturn(false);

        $twig->getLoader()->shouldNotBeCalled();

        $this->findTemplate(404)->shouldReturn(null);
    }

    function it_finds_template_for_admin(
        SectionProviderInterface $sectionProvider,
        LoggedInUserProviderInterface $loggedInUserProvider,
        Environment $twig,
        LoaderInterface $loader,
    ): void {
        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';

        $sectionProvider->getSection()->willReturn(new AdminSection());
        $loggedInUserProvider->hasUser()->willReturn(true);

        $twig->getLoader()->willReturn($loader);
        $loader->exists($templateName)->shouldBeCalled()->willReturn(true);

        $this->findTemplate(404)->shouldReturn($templateName);
    }

    function it_returns_null_if_neither_template_can_be_found(
        SectionProviderInterface $sectionProvider,
        LoggedInUserProviderInterface $loggedInUserProvider,
        Environment $twig,
        LoaderInterface $loader,
    ): void {
        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';
        $fallbackTemplateName = self::TEMPLATE_PREFIX . '/error.html.twig';

        $sectionProvider->getSection()->willReturn(new AdminSection());
        $loggedInUserProvider->hasUser()->willReturn(true);

        $twig->getLoader()->willReturn($loader);
        $loader->exists($templateName)->shouldBeCalled()->willReturn(false);
        $loader->exists($fallbackTemplateName)->shouldBeCalled()->willReturn(false);

        $this->findTemplate(404)->shouldReturn(null);
    }

    function it_finds_fallback_template_for_admin(
        SectionProviderInterface $sectionProvider,
        LoggedInUserProviderInterface $loggedInUserProvider,
        Environment $twig,
        LoaderInterface $loader,
    ): void {
        $templateName = self::TEMPLATE_PREFIX . '/error404.html.twig';
        $fallbackTemplateName = self::TEMPLATE_PREFIX . '/error.html.twig';

        $sectionProvider->getSection()->willReturn(new AdminSection());
        $loggedInUserProvider->hasUser()->willReturn(true);

        $twig->getLoader()->willReturn($loader);
        $loader->exists($templateName)->shouldBeCalled()->willReturn(false);
        $loader->exists($fallbackTemplateName)->shouldBeCalled()->willReturn(true);

        $this->findTemplate(404)->shouldReturn($fallbackTemplateName);
    }
}
