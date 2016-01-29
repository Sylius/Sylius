<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\Renderer\Custom;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Metadata\Model\Custom\PageMetadataInterface;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\Twitter\CardInterface;
use Sylius\Component\Metadata\Renderer\MetadataRendererInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @mixin \Sylius\Component\Metadata\Renderer\Custom\PageMetadataRenderer
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PageMetadataRendererSpec extends ObjectBehavior
{
    private $defaultOptions = ['group' => 'head', 'defaults' => []];

    function let(MetadataRendererInterface $universalRenderer, OptionsResolver $optionsResolver, PropertyAccessorInterface $propertyAccessor)
    {
        $this->beConstructedWith($universalRenderer, $optionsResolver, $propertyAccessor);

        $optionsResolver->setDefaults(Argument::type('array'))->willReturn($optionsResolver);
        $optionsResolver->setAllowedValues(Argument::any(), Argument::type('array'))->willReturn($optionsResolver);
        $optionsResolver->setAllowedTypes(Argument::any(), Argument::any())->willReturn($optionsResolver);
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
        PageMetadataInterface $pageMetadata,
        MetadataInterface $randomMetadata
    ) {
        $this->supports($pageMetadata)->shouldReturn(true);
        $this->supports($randomMetadata)->shouldReturn(false);
    }

    function it_throws_an_exception_if_metadata_has_unsupported_properties(
        OptionsResolver $optionsResolver,
        PageMetadataInterface $pageMetadata,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn($this->defaultOptions);

        $data = [
            'title' => 'Lorem ipsum',
            'notexisting' => '42',
        ];

        $this->setupPropertyAccessor($propertyAccessor, $pageMetadata, $data, $this->defaultOptions);

        $pageMetadata->toArray()->shouldBeCalled()->willReturn($data);

        $this->shouldThrow('\InvalidArgumentException')->duringRender($pageMetadata);
    }

    function it_renders_valid_metadata(
        OptionsResolver $optionsResolver,
        PageMetadataInterface $pageMetadata,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn($this->defaultOptions);

        $data = [
            'title' => 'Lorem ipsum',
            'keywords' => ['foo', 'bar'],
            'author' => 'Krzysztof Krawczyk',
        ];

        $this->setupPropertyAccessor($propertyAccessor, $pageMetadata, $data, $this->defaultOptions);

        $pageMetadata->toArray()->shouldBeCalled()->willReturn($data);

        $renderedMetadata = $this->render($pageMetadata);
        $renderedMetadata->shouldContainText('<title>Lorem ipsum</title>');
        $renderedMetadata->shouldContainText('<meta name="keywords" content="foo, bar" />');
        $renderedMetadata->shouldContainText('<meta name="author" content="Krzysztof Krawczyk" />');
    }

    function it_does_not_render_null_values(
        OptionsResolver $optionsResolver,
        PropertyAccessorInterface $propertyAccessor,
        PageMetadataInterface $pageMetadata
    ) {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn($this->defaultOptions);

        $data = [
            'title' => 'Lorem ipsum',
            'description' => null,
        ];

        $this->setupPropertyAccessor($propertyAccessor, $pageMetadata, $data, $this->defaultOptions);

        $pageMetadata->toArray()->shouldBeCalled()->willReturn($data);

        $renderedMetadata = $this->render($pageMetadata);
        $renderedMetadata->shouldContainText('<title>Lorem ipsum</title>');
    }

    function it_delegates_twitter_metadata_rendering_to_another_renderer(
        MetadataRendererInterface $universalRenderer,
        OptionsResolver $optionsResolver,
        PropertyAccessorInterface $propertyAccessor,
        PageMetadataInterface $pageMetadata,
        CardInterface $twitterMetadata
    ) {
        $optionsResolver->resolve([])->shouldBeCalled()->willReturn($this->defaultOptions);

        $data = ['twitter' => $twitterMetadata];

        $this->setupPropertyAccessor($propertyAccessor, $pageMetadata, $data, $this->defaultOptions);

        $pageMetadata->toArray()->shouldBeCalled()->willReturn($data);

        $universalRenderer->render($twitterMetadata)->shouldBeCalled()->willReturn('Rendered Twitter metadata');

        $this->render($pageMetadata)->shouldReturn('Rendered Twitter metadata');
    }

    function it_uses_defaults_option_to_set_default_values_while_rendering(
        OptionsResolver $optionsResolver,
        PageMetadataInterface $pageMetadata,
        PropertyAccessorInterface $propertyAccessor
    ) {
        $options = [
            'defaults' => [
                'author' => 'Krzysztof Krawczyk',
            ],
        ];

        $resolvedOptions = array_merge($this->defaultOptions, $options);

        $optionsResolver->resolve($options)->shouldBeCalled()->willReturn($resolvedOptions);

        $data = [
            'title' => 'Lorem ipsum',
            'author' => null,
        ];

        $this->setupPropertyAccessor($propertyAccessor, $pageMetadata, $data, $resolvedOptions);

        $pageMetadata->toArray()->shouldBeCalled()->willReturn($data);

        $renderedMetadata = $this->render($pageMetadata, $options);
        $renderedMetadata->shouldContainText('<title>Lorem ipsum</title>');
        $renderedMetadata->shouldContainText('<meta name="author" content="Krzysztof Krawczyk" />');
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers()
    {
        return [
            'containText' => function ($subject, $text) {
                return false !== strpos($subject, $text);
            },
        ];
    }

    /**
     * @param PropertyAccessorInterface $propertyAccessor
     * @param MetadataInterface $metadata
     * @param array $data
     * @param array $options
     */
    private function setupPropertyAccessor(
        PropertyAccessorInterface $propertyAccessor,
        MetadataInterface $metadata,
        array $data,
        array $options
    ) {
        foreach ($options['defaults'] as $key => $value) {
            if (isset($data[$key]) && null !== $data[$key]) {
                continue;
            }

            $propertyAccessor->setValue($metadata, $key, $value)->shouldBeCalled();
            $propertyAccessor->getValue($metadata, $key)->shouldBeCalled()->willReturn(null, $value);
        }

        foreach ($data as $key => $value) {
            if (isset($options['defaults'][$key]) && null === $value) {
                continue;
            }

            $propertyAccessor->getValue($metadata, $key)->shouldBeCalled()->willReturn($value);
        }
    }
}
