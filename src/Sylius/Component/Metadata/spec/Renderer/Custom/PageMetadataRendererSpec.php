<?php

namespace spec\Sylius\Component\Metadata\Renderer\Custom;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Metadata\Model\Custom\PageMetadataInterface;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\Twitter\CardInterface;
use Sylius\Component\Metadata\Renderer\MetadataRendererInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin \Sylius\Component\Metadata\Renderer\Custom\PageMetadataRenderer
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PageMetadataRendererSpec extends ObjectBehavior
{
    function let(MetadataRendererInterface $universalRenderer, OptionsResolver $optionsResolver)
    {
        $this->beConstructedWith($universalRenderer, $optionsResolver);

        $optionsResolver->setDefaults(Argument::type('array'))->willReturn($optionsResolver);
        $optionsResolver->setAllowedValues(Argument::any(), Argument::type('array'))->willReturn($optionsResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Renderer\Custom\PageMetadataRenderer');
    }

    function it_implements_Metadata_Renderer_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Renderer\MetadataRendererInterface');
    }

    function it_checks_if_metadata_is_supported_or_not(
        OptionsResolver $optionsResolver,
        PageMetadataInterface $pageMetadata,
        MetadataInterface $randomMetadata
    ) {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn([]);

        $this->supports($pageMetadata)->shouldReturn(true);
        $this->supports($randomMetadata)->shouldReturn(false);
    }

    function it_throws_an_exception_if_metadata_has_unsupported_properties(OptionsResolver $optionsResolver, PageMetadataInterface $pageMetadata)
    {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn([]);

        $pageMetadata->toArray()->shouldBeCalled()->willReturn(['title' => 'Lorem ipsum', 'notexisting' => '42']);

        $this->shouldThrow('\InvalidArgumentException')->duringRender($pageMetadata);
    }

    function it_renders_valid_metadata(OptionsResolver $optionsResolver, PageMetadataInterface $pageMetadata)
    {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn([]);

        $pageMetadata->toArray()->shouldBeCalled()->willReturn([
            'title' => 'Lorem ipsum',
            'keywords' => ['foo', 'bar'],
            'author' => 'Krzysztof Krawczyk',
        ]);

        $this->render($pageMetadata)->shouldReturn(
            '<title>Lorem ipsum</title>' . "\n" .
            '<meta name="keywords" content="foo, bar" />' . "\n" .
            '<meta name="author" content="Krzysztof Krawczyk" />'
        );
    }

    function it_does_not_render_null_values(OptionsResolver $optionsResolver, PageMetadataInterface $pageMetadata)
    {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn([]);

        $pageMetadata->toArray()->shouldBeCalled()->willReturn([
            'title' => 'Lorem ipsum',
            'description' => null,
        ]);

        $this->render($pageMetadata)->shouldReturn('<title>Lorem ipsum</title>');
    }

    function it_delegates_twitter_metadata_rendering_to_another_renderer(
        MetadataRendererInterface $universalRenderer,
        OptionsResolver $optionsResolver,
        PageMetadataInterface $pageMetadata,
        CardInterface $twitterMetadata
    ) {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn([]);

        $pageMetadata->toArray()->shouldBeCalled()->willReturn(['twitter' => $twitterMetadata]);

        $universalRenderer->render($twitterMetadata)->shouldBeCalled()->willReturn('Rendered Twitter metadata');

        $this->render($pageMetadata)->shouldReturn('Rendered Twitter metadata');
    }
}
