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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\DoctrineCollectionValuesNormalizer;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class DoctrineCollectionValuesNormalizerTest extends TestCase
{
    private DoctrineCollectionValuesNormalizer $doctrineCollectionValuesNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->doctrineCollectionValuesNormalizer = new DoctrineCollectionValuesNormalizer();
    }

    public function testSupportsOnlyDoctrineCollectionWithNormalizationContextKey(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var Collection|MockObject $collectionMock */
        $collectionMock = $this->createMock(Collection::class);

        self::assertFalse($this->doctrineCollectionValuesNormalizer->supportsNormalization($orderMock));

        self::assertFalse(
            $this->doctrineCollectionValuesNormalizer->supportsNormalization(
                $orderMock,
                null,
                ['collection_values' => false],
            ),
        );

        self::assertFalse(
            $this->doctrineCollectionValuesNormalizer->supportsNormalization(
                $orderMock,
                null,
                ['collection_values' => true],
            ),
        );

        self::assertFalse($this->doctrineCollectionValuesNormalizer->supportsNormalization($collectionMock));

        self::assertFalse(
            $this->doctrineCollectionValuesNormalizer->supportsNormalization(
                $collectionMock,
                null,
                ['collection_values' => false],
            ),
        );

        self::assertTrue(
            $this->doctrineCollectionValuesNormalizer->supportsNormalization(
                $collectionMock,
                null,
                ['collection_values' => true],
            ),
        );
    }

    public function testNormalizesCollectionValues(): void
    {
        /** @var NormalizerInterface|MockObject $normalizerMock */
        $normalizerMock = $this->createMock(NormalizerInterface::class);

        $this->doctrineCollectionValuesNormalizer->setNormalizer($normalizerMock);

        $normalizerMock->expects(self::once())
            ->method('normalize')
            ->with([['id' => 1], ['id' => 2]], null, ['collection_values' => true])
            ->willReturn([]);

        $this->doctrineCollectionValuesNormalizer->normalize(
            new ArrayCollection(['1' => ['id' => 1], '2' => ['id' => 2]]),
            null,
            ['collection_values' => true],
        );
    }
}
