<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\Renderer\Twitter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\Twitter\CardInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin \Sylius\Component\Metadata\Renderer\Twitter\GenericTwitterMetadataRenderer
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class GenericTwitterMetadataRendererSpec extends ObjectBehavior
{
    function let(OptionsResolver $optionsResolver)
    {
        $this->beConstructedWith($optionsResolver);

        $optionsResolver->setDefaults(Argument::type('array'))->willReturn($optionsResolver);
        $optionsResolver->setAllowedValues(Argument::any(), Argument::type('array'))->willReturn($optionsResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Renderer\Twitter\GenericTwitterMetadataRenderer');
    }

    function it_implements_Metadata_Renderer_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Renderer\MetadataRendererInterface');
    }

    function it_checks_if_metadata_is_supported_or_not(
        OptionsResolver $optionsResolver,
        CardInterface $twitterMetadata,
        MetadataInterface $randomMetadata
    ) {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn([]);

        $this->supports($twitterMetadata)->shouldReturn(true);
        $this->supports($randomMetadata)->shouldReturn(false);
    }

    function it_throws_an_exception_if_metadata_has_unsupported_properties(OptionsResolver $optionsResolver, CardInterface $twitterMetadata)
    {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn([]);

        $twitterMetadata->toArray()->shouldBeCalled()->willReturn(['type' => 'summary', 'notexisting' => '42']);

        $this->shouldThrow('\InvalidArgumentException')->duringRender($twitterMetadata);
    }

    function it_renders_valid_metadata(OptionsResolver $optionsResolver, CardInterface $twitterMetadata)
    {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn([]);

        $twitterMetadata->toArray()->shouldBeCalled()->willReturn(['type' => 'summary', 'title' => 'Lorem ipsum']);

        $this->render($twitterMetadata)->shouldReturn(
            '<meta name="twitter:card" content="summary" />' . "\n" .
            '<meta name="twitter:title" content="Lorem ipsum" />'
        );
    }

    function it_does_not_render_null_values(OptionsResolver $optionsResolver, CardInterface $twitterMetadata)
    {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn([]);

        $twitterMetadata->toArray()->shouldBeCalled()->willReturn([
            'type' => 'summary',
            'description' => null,
            'title' => 'Lorem ipsum',
        ]);

        $this->render($twitterMetadata)->shouldReturn(
            '<meta name="twitter:card" content="summary" />' . "\n" .
            '<meta name="twitter:title" content="Lorem ipsum" />'
        );
    }
}
