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

namespace spec\Sylius\Bundle\ApiBundle\Swagger;

use ApiPlatform\Core\Documentation\Documentation;
use ApiPlatform\Core\Metadata\Resource\ResourceNameCollection;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class AcceptLanguageHeaderDocumentationNormalizerSpec extends ObjectBehavior
{
    function let(NormalizerInterface $decoratedNormalizer): void
    {
        $this->beConstructedWith($decoratedNormalizer);
    }

    function it_supports_normalization(NormalizerInterface $decoratedNormalizer): void
    {
        $documentation = new Documentation(new ResourceNameCollection());

        $decoratedNormalizer->supportsNormalization($documentation, null)->willReturn(true);

        $this->supportsNormalization($documentation)->shouldReturn(true);
    }

    function it_does_not_support_normalization(NormalizerInterface $decoratedNormalizer): void
    {
        $decoratedNormalizer->supportsNormalization(null, null)->willReturn(false);

        $this->supportsNormalization(null)->shouldReturn(false);
    }

    function it_does_not_add_accept_language_header_to_paths_which_are_not_objects(
        NormalizerInterface $decoratedNormalizer,
    ): void {
        $docs = [
            'paths' => [
                '/api/v2/admin/addresses/{id}' => [
                    'get' => [
                        'parameters' => [],
                    ],
                ],
            ],
        ];

        $documentation = new Documentation(new ResourceNameCollection());

        $decoratedNormalizer
            ->normalize($documentation, null, ['spec_version' => 3])
            ->willReturn($docs)
        ;

        $this
            ->normalize($documentation, null, ['spec_version' => 3])
            ->shouldReturn([
                'paths' => [
                    '/api/v2/admin/addresses/{id}' => [
                        'get' => [
                            'parameters' => [],
                        ],
                    ],
                ],
            ])
        ;
    }

    function it_adds_accept_language_header_to_paths_which_are_objects(NormalizerInterface $decoratedNormalizer,): void
    {
        $docs = [
            'paths' => [
                '/api/v2/admin/addresses/{id}' => [
                    'get' => new \ArrayObject(
                        [
                            'parameters' => [],
                        ],
                    )
                ],
            ],
        ];

        $documentation = new Documentation(new ResourceNameCollection());

        $decoratedNormalizer
            ->normalize($documentation, null, ['spec_version' => 3])
            ->willReturn($docs)
        ;

        $this
            ->normalize($documentation, null, ['spec_version' => 3])
            ->shouldReturn([
                'paths' => [
                    '/api/v2/admin/addresses/{id}' => [
                        'get' => [
                            'parameters' => [
                                [
                                    'name' => 'Accept-Language',
                                    'in' => 'header',
                                    'required' => false,
                                    'schema' => [
                                        'type' => 'string'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ])
        ;
    }
}
