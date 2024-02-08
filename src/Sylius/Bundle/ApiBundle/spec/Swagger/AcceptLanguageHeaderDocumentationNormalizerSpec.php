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

namespace spec\Sylius\Bundle\ApiBundle\Swagger;

use ApiPlatform\Core\Documentation\Documentation;
use ApiPlatform\Core\Metadata\Resource\ResourceNameCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class AcceptLanguageHeaderDocumentationNormalizerSpec extends ObjectBehavior
{
    function let(NormalizerInterface $decoratedNormalizer, RepositoryInterface $localeRepository): void
    {
        $this->beConstructedWith($decoratedNormalizer, $localeRepository);
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
        RepositoryInterface $localeRepository,
        LocaleInterface $locale1,
        LocaleInterface $locale2,
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

        $localeRepository->findAll()->willReturn([$locale1, $locale2]);

        $locale1->getCode()->willReturn('en_US');
        $locale2->getCode()->willReturn('de_DE');

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

    function it_adds_accept_language_header_to_paths_which_are_objects(
        NormalizerInterface $decoratedNormalizer,
        RepositoryInterface $localeRepository,
        LocaleInterface $locale1,
        LocaleInterface $locale2,
    ): void {
        $docs = [
            'paths' => [
                '/api/v2/admin/addresses/{id}' => [
                    'get' => new \ArrayObject(
                        [
                            'parameters' => [],
                        ],
                    ),
                ],
            ],
        ];

        $documentation = new Documentation(new ResourceNameCollection());

        $localeRepository->findAll()->willReturn([$locale1, $locale2]);

        $locale1->getCode()->willReturn('en_US');
        $locale2->getCode()->willReturn('de_DE');

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
                                    'description' => 'Locales in this enum are all locales defined in the shop and only enabled ones will work in the given channel in the shop.',
                                    'schema' => [
                                        'type' => 'string',
                                        'enum' => ['en_US', 'de_DE'],
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
