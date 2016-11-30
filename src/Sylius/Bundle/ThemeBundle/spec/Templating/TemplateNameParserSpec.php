<?php

namespace spec\Sylius\Bundle\ThemeBundle\Templating;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Templating\TemplateNameParser;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TemplateNameParserSpec extends ObjectBehavior
{
    function let(TemplateNameParserInterface $decoratedParser, KernelInterface $kernel)
    {
        $this->beConstructedWith($decoratedParser, $kernel);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TemplateNameParser::class);
    }

    function it_is_a_template_name_parser()
    {
        $this->shouldImplement(TemplateNameParserInterface::class);
    }

    function it_returns_template_reference_if_passed_as_name(TemplateReferenceInterface $templateReference)
    {
        $this->parse($templateReference)->shouldReturn($templateReference);
    }

    function it_delegates_logical_paths_to_decorated_parser(
        TemplateNameParserInterface $decoratedParser,
        TemplateReferenceInterface $templateReference
    ) {
        $decoratedParser->parse('Bundle:Not:namespaced.html.twig')->willReturn($templateReference);

        $this->parse('Bundle:Not:namespaced.html.twig')->shouldReturn($templateReference);
    }

    function it_delegates_unknown_paths_to_decorated_parser(
        TemplateNameParserInterface $decoratedParser,
        TemplateReferenceInterface $templateReference
    ) {
        $decoratedParser->parse('Bundle/Not/namespaced.html.twig')->willReturn($templateReference);

        $this->parse('Bundle/Not/namespaced.html.twig')->shouldReturn($templateReference);
    }

    function it_generates_template_references_from_namespaced_paths(KernelInterface $kernel)
    {
        $kernel->getBundle('AcmeBundle')->willReturn(null); // just do not throw an exception

        $this->parse('@Acme/app.html.twig')->shouldBeLike(new TemplateReference('AcmeBundle', '', 'app', 'html', 'twig'));
        $this->parse('@Acme/Directory/app.html.twig')->shouldBeLike(new TemplateReference('AcmeBundle', 'Directory', 'app', 'html', 'twig'));
        $this->parse('@Acme/Directory.WithDot/app.html.twig')->shouldBeLike(new TemplateReference('AcmeBundle', 'Directory.WithDot', 'app', 'html', 'twig'));
        $this->parse('@Acme/Directory/app.with.dots.html.twig')->shouldBeLike(new TemplateReference('AcmeBundle', 'Directory', 'app.with.dots', 'html', 'twig'));
        $this->parse('@Acme/Nested/Directory/app.html.twig')->shouldBeLike(new TemplateReference('AcmeBundle', 'Nested/Directory', 'app', 'html', 'twig'));
    }

    function it_throws_an_exception_if_namespaced_path_references_not_existing_bundle(KernelInterface $kernel)
    {
        $kernel->getBundle('AcmeBundle')->willThrow(\Exception::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('parse', ['@Acme/app.html.twig']);
    }

    function it_generates_template_references_from_root_namespaced_paths()
    {
        $this->parse('/app.html.twig')->shouldBeLike(new TemplateReference('', '', 'app', 'html', 'twig'));
        $this->parse('/Directory/app.html.twig')->shouldBeLike(new TemplateReference('', 'Directory', 'app', 'html', 'twig'));
        $this->parse('/Nested/Directory/app.html.twig')->shouldBeLike(new TemplateReference('', 'Nested/Directory', 'app', 'html', 'twig'));
        $this->parse('/Directory.WithDot/app.html.twig')->shouldBeLike(new TemplateReference('', 'Directory.WithDot', 'app', 'html', 'twig'));
    }
}
