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

namespace spec\Sylius\Bundle\ThemeBundle\Templating;

use PhpSpec\ObjectBehavior;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

final class TemplateNameParserSpec extends ObjectBehavior
{
    function let(TemplateNameParserInterface $decoratedParser, KernelInterface $kernel): void
    {
        $this->beConstructedWith($decoratedParser, $kernel);
    }

    function it_is_a_template_name_parser(): void
    {
        $this->shouldImplement(TemplateNameParserInterface::class);
    }

    function it_returns_template_reference_if_passed_as_name(TemplateReferenceInterface $templateReference): void
    {
        $this->parse($templateReference)->shouldReturn($templateReference);
    }

    function it_delegates_logical_paths_to_decorated_parser(
        TemplateNameParserInterface $decoratedParser,
        TemplateReferenceInterface $templateReference
    ): void {
        $decoratedParser->parse('Bundle:Not:namespaced.html.twig')->willReturn($templateReference);

        $this->parse('Bundle:Not:namespaced.html.twig')->shouldReturn($templateReference);
    }

    function it_delegates_unknown_paths_to_decorated_parser(
        TemplateNameParserInterface $decoratedParser,
        TemplateReferenceInterface $templateReference
    ): void {
        $decoratedParser->parse('Bundle/Not/namespaced.html.twig')->willReturn($templateReference);

        $this->parse('Bundle/Not/namespaced.html.twig')->shouldReturn($templateReference);
    }

    function it_generates_template_references_from_namespaced_paths(KernelInterface $kernel): void
    {
        $kernel->getBundle('AcmeBundle')->willReturn(null); // just do not throw an exception

        $this->parse('@Acme/app.html.twig')->shouldBeLike(new TemplateReference('AcmeBundle', '', 'app', 'html', 'twig'));
        $this->parse('@Acme/Directory/app.html.twig')->shouldBeLike(new TemplateReference('AcmeBundle', 'Directory', 'app', 'html', 'twig'));
        $this->parse('@Acme/Directory.WithDot/app.html.twig')->shouldBeLike(new TemplateReference('AcmeBundle', 'Directory.WithDot', 'app', 'html', 'twig'));
        $this->parse('@Acme/Directory/app.with.dots.html.twig')->shouldBeLike(new TemplateReference('AcmeBundle', 'Directory', 'app.with.dots', 'html', 'twig'));
        $this->parse('@Acme/Nested/Directory/app.html.twig')->shouldBeLike(new TemplateReference('AcmeBundle', 'Nested/Directory', 'app', 'html', 'twig'));
    }

    function it_delegates_custom_namespace_to_decorated_parser(
        KernelInterface $kernel,
        TemplateNameParserInterface $decoratedParser,
        TemplateReferenceInterface $templateReference
    ): void {
        $kernel->getBundle('myBundle')->willThrow(\Exception::class);

        $decoratedParser->parse('@my/custom/namespace.html.twig')->willReturn($templateReference);

        $this->parse('@my/custom/namespace.html.twig')->shouldReturn($templateReference);
    }

    function it_generates_template_references_from_root_namespaced_paths(): void
    {
        $this->parse('/app.html.twig')->shouldBeLike(new TemplateReference('', '', 'app', 'html', 'twig'));
        $this->parse('/Directory/app.html.twig')->shouldBeLike(new TemplateReference('', 'Directory', 'app', 'html', 'twig'));
        $this->parse('/Nested/Directory/app.html.twig')->shouldBeLike(new TemplateReference('', 'Nested/Directory', 'app', 'html', 'twig'));
        $this->parse('/Directory.WithDot/app.html.twig')->shouldBeLike(new TemplateReference('', 'Directory.WithDot', 'app', 'html', 'twig'));
    }
}
