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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Catalog\AddProductReview;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverterInterface;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandArgumentsDenormalizer;
use Sylius\Component\Core\Model\Order;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandArgumentsDenormalizerTest extends TestCase
{
    private DenormalizerInterface&MockObject $commandDenormalizer;

    private IriToIdentifierConverterInterface&MockObject $iriToIdentifierConverter;

    private CommandArgumentsDenormalizer $commandArgumentsDenormalizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commandDenormalizer = $this->createMock(DenormalizerInterface::class);
        $this->iriToIdentifierConverter = $this->createMock(IriToIdentifierConverterInterface::class);
        $this->commandArgumentsDenormalizer = new CommandArgumentsDenormalizer(
            $this->commandDenormalizer,
            $this->iriToIdentifierConverter,
        );
    }

    public function testSupportsDenormalizationAddProductReview(): void
    {
        $context = ['input' => ['class' => AddProductReview::class]];

        self::assertTrue($this->commandArgumentsDenormalizer
            ->supportsDenormalization(
                new AddProductReview('Cap', 5, 'ok', 'cap_code', 'john@example.com'),
                AddProductReview::class,
                null,
                $context,
            ));
    }

    public function testDoesNotSupportDenormalizationForNotSupportedClass(): void
    {
        $context = ['input' => ['class' => Order::class]];

        self::assertFalse($this->commandArgumentsDenormalizer
            ->supportsDenormalization(
                new Order(),
                AddProductReview::class,
                null,
                $context,
            ));
    }

    public function testDenormalizesAddProductReviewAndConvertsProductFieldFromIriToCode(): void
    {
        $context = ['input' => ['class' => AddProductReview::class]];
        $addProductReview = new AddProductReview('Cap', 5, 'ok', 'cap_code', 'john@example.com');

        $this->iriToIdentifierConverter
            ->expects(self::exactly(4))
            ->method('isIdentifier')
            ->willReturnMap([
                ['Cap', false],
                ['ok', false],
                ['/api/v2/shop/products/cap_code', true],
                ['john@example.com', false],
            ]);

        $this->iriToIdentifierConverter
            ->expects(self::once())
            ->method('getIdentifier')
            ->with('/api/v2/shop/products/cap_code')
            ->willReturn('cap_code');

        $this->commandDenormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with([
                'title' => 'Cap',
                'rating' => 5,
                'comment' => 'ok',
                'product' => 'cap_code',
                'email' => 'john@example.com',
            ], AddProductReview::class, null, $context)
            ->willReturn($addProductReview);

        $result = $this->commandArgumentsDenormalizer->denormalize(
            [
                'title' => 'Cap',
                'rating' => 5,
                'comment' => 'ok',
                'product' => '/api/v2/shop/products/cap_code',
                'email' => 'john@example.com',
            ],
            AddProductReview::class,
            null,
            $context,
        );

        self::assertSame($addProductReview, $result);
    }

    public function testDenormalizesACommandWithAnArrayOfIris(): void
    {
        $command = new class() implements IriToIdentifierConversionAwareInterface {
            public string $iri = '/api/v2/iri';

            public array $arrayIris = [
                '/api/v2/first-iri',
                '/api/v2/second-iri',
            ];

            public array $arrayField = ['array'];
        };

        $context = ['input' => ['class' => $command::class]];

        $this->iriToIdentifierConverter
            ->method('isIdentifier')
            ->willReturnCallback(function ($value) {
                return match ($value) {
                    '/api/v2/iri', '/api/v2/first-iri', '/api/v2/second-iri' => true,
                    'array', '' => false,
                    default => false,
                };
            });

        $this->iriToIdentifierConverter
            ->method('getIdentifier')
            ->willReturnCallback(function ($value) {
                return match ($value) {
                    '/api/v2/iri' => 'iri',
                    '/api/v2/first-iri' => 'first-iri',
                    '/api/v2/second-iri' => 'second-iri',
                    default => null,
                };
            });

        $this->commandDenormalizer
            ->expects(self::once())
            ->method('denormalize')
            ->with(
                [
                    'iri' => 'iri',
                    'arrayIris' => ['first-iri', 'second-iri', ''],
                    'arrayField' => ['array'],
                ],
                $command::class,
                null,
                $context,
            )
            ->willReturn($command);

        $result = $this->commandArgumentsDenormalizer->denormalize(
            [
                'iri' => '/api/v2/iri',
                'arrayIris' => [
                    '/api/v2/first-iri',
                    '/api/v2/second-iri',
                    '',
                ],
                'arrayField' => ['array'],
            ],
            $command::class,
            null,
            $context,
        );

        self::assertSame($command, $result);
    }
}
