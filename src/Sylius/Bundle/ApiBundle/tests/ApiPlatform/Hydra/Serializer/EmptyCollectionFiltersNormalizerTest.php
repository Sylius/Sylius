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

namespace Tests\Sylius\Bundle\ApiBundle\ApiPlatform\Hydra\Serializer;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\ApiPlatform\Hydra\Serializer\EmptyCollectionFiltersNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class EmptyCollectionFiltersNormalizerTest extends TestCase
{
    private MockObject&NormalizerInterface $decorated;

    private EmptyCollectionFiltersNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(NormalizerInterface::class);
        $this->normalizer = new EmptyCollectionFiltersNormalizer($this->decorated);
    }

    #[DataProvider('getNormalizationCases')]
    public function test_normalize_behaviors(array $normalizedData, array $expectedResult): void
    {
        $this->decorated->method('normalize')->willReturn($normalizedData);

        $result = $this->normalizer->normalize(['any']);

        self::assertSame($expectedResult, $result);
    }

    public static function getNormalizationCases(): iterable
    {
        yield 'search is null' => [
            ['hydra:search' => null],
            ['hydra:search' => null],
        ];

        yield 'search is empty array' => [
            ['hydra:search' => []],
            ['hydra:search' => []],
        ];

        yield 'iri is empty' => [
            ['@id' => '', 'hydra:search' => ['some_key' => 'some_val']],
            ['@id' => '', 'hydra:search' => ['some_key' => 'some_val']],
        ];

        yield 'mapping and template both empty' => [
            [
                '@id' => '/products',
                'hydra:search' => [
                    'hydra:mapping' => [],
                    'hydra:template' => '',
                ],
            ],
            ['@id' => '/products'],
        ];

        yield 'mapping is not empty, template empty' => [
            [
                '@id' => '/products',
                'hydra:search' => [
                    'hydra:mapping' => ['some' => 'thing'],
                    'hydra:template' => '',
                ],
            ],
            [
                '@id' => '/products',
                'hydra:search' => [
                    'hydra:mapping' => ['some' => 'thing'],
                    'hydra:template' => '',
                ],
            ],
        ];

        yield 'template matches iri + {?}' => [
            [
                '@id' => '/products',
                'hydra:search' => [
                    'hydra:mapping' => [],
                    'hydra:template' => '/products{?}',
                ],
            ],
            ['@id' => '/products'],
        ];

        yield 'template does not match iri + {?}' => [
            [
                '@id' => '/products',
                'hydra:search' => [
                    'hydra:mapping' => [],
                    'hydra:template' => '/products{?field}',
                ],
            ],
            [
                '@id' => '/products',
                'hydra:search' => [
                    'hydra:mapping' => [],
                    'hydra:template' => '/products{?field}',
                ],
            ],
        ];

        yield 'mapping is not empty and template does not match iri' => [
            [
                '@id' => '/products',
                'hydra:search' => [
                    'hydra:mapping' => ['field' => 'value'],
                    'hydra:template' => '/products{?field}',
                ],
            ],
            [
                '@id' => '/products',
                'hydra:search' => [
                    'hydra:mapping' => ['field' => 'value'],
                    'hydra:template' => '/products{?field}',
                ],
            ],
        ];
    }
}
