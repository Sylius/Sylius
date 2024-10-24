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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Catalog\AddProductReview;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Converter\IriToIdentifierConverterInterface;
use Sylius\Component\Core\Model\Order;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandArgumentsDenormalizerSpec extends ObjectBehavior
{
    function let(
        DenormalizerInterface $commandDenormalizer,
        IriToIdentifierConverterInterface $iriToIdentifierConverter,
    ): void {
        $this->beConstructedWith(
            $commandDenormalizer,
            $iriToIdentifierConverter,
        );
    }

    function it_supports_denormalization_add_product_review(): void
    {
        $context = ['input' => ['class' => AddProductReview::class]];

        $this
            ->supportsDenormalization(
                new AddProductReview('Cap', 5, 'ok', 'cap_code', 'john@example.com'),
                AddProductReview::class,
                null,
                $context,
            )
            ->shouldReturn(true)
        ;
    }

    function it_does_not_support_denormalization_for_not_supported_class(): void
    {
        $context = ['input' => ['class' => Order::class]];

        $this
            ->supportsDenormalization(
                new Order(),
                AddProductReview::class,
                null,
                $context,
            )
            ->shouldReturn(false)
        ;
    }

    function it_denormalizes_add_product_review_and_converts_product_field_from_iri_to_code(
        DenormalizerInterface $commandDenormalizer,
        IriToIdentifierConverterInterface $iriToIdentifierConverter,
    ): void {
        $context = ['input' => ['class' => AddProductReview::class]];

        $addProductReview = new AddProductReview('Cap', 5, 'ok', 'cap_code', 'john@example.com');

        $iriToIdentifierConverter->isIdentifier('Cap')->willReturn(false);
        $iriToIdentifierConverter->isIdentifier(5)->willReturn(false);
        $iriToIdentifierConverter->isIdentifier('ok')->willReturn(false);
        $iriToIdentifierConverter->isIdentifier('john@example.com')->willReturn(false);
        $iriToIdentifierConverter->isIdentifier('/api/v2/shop/products/cap_code')->willReturn(true);
        $iriToIdentifierConverter->getIdentifier('/api/v2/shop/products/cap_code')->willReturn('cap_code');

        $commandDenormalizer
            ->denormalize(
                [
                    'title' => 'Cap',
                    'rating' => 5,
                    'comment' => 'ok',
                    'product' => 'cap_code',
                    'email' => 'john@example.com',
                ],
                AddProductReview::class,
                null,
                $context,
            )
            ->willReturn($addProductReview)
        ;

        $this
            ->denormalize(
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
            )
            ->shouldReturn($addProductReview)
        ;
    }

    function it_denormalizes_a_command_with_an_array_of_iris(
        DenormalizerInterface $commandDenormalizer,
        IriToIdentifierConverterInterface $iriToIdentifierConverter,
    ): void {
        $command = new class() implements IriToIdentifierConversionAwareInterface {
            public string $iri = '/api/v2/iri';

            public array $arrayIris = [
                '/api/v2/first-iri',
                '/api/v2/second-iri',
            ];

            public array $arrayField = ['array'];
        };
        $context = ['input' => ['class' => $command::class]];

        $iriToIdentifierConverter->isIdentifier('array')->willReturn(false);

        $iriToIdentifierConverter->isIdentifier('/api/v2/iri')->willReturn(true);
        $iriToIdentifierConverter->getIdentifier('/api/v2/iri')->willReturn('iri');

        $iriToIdentifierConverter->isIdentifier('/api/v2/first-iri')->willReturn(true);
        $iriToIdentifierConverter->getIdentifier('/api/v2/first-iri')->willReturn('first-iri');

        $iriToIdentifierConverter->isIdentifier('')->willReturn(false);
        $iriToIdentifierConverter->getIdentifier('')->shouldNotBeCalled();

        $iriToIdentifierConverter->isIdentifier('/api/v2/second-iri')->willReturn(true);
        $iriToIdentifierConverter->getIdentifier('/api/v2/second-iri')->willReturn('second-iri');

        $commandDenormalizer
            ->denormalize(
                [
                    'iri' => 'iri',
                    'arrayIris' => [
                        'first-iri',
                        'second-iri',
                        '',
                    ],
                    'arrayField' => ['array'],
                ],
                $command::class,
                null,
                $context,
            )
            ->willReturn($command)
        ;

        $this
            ->denormalize(
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
            )
            ->shouldReturn($command)
        ;
    }
}
