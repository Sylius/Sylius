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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\AddProductReview;
use Sylius\Bundle\ApiBundle\Converter\ItemIriToIdentifierConverterInterface;
use Sylius\Component\Core\Model\Order;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandFieldItemIriToIdentifierDenormalizerSpec extends ObjectBehavior
{
    function let(
        DenormalizerInterface $objectNormalizer,
        ItemIriToIdentifierConverterInterface $itemIriToIdentifierConverter,
        DataTransformerInterface $commandAwareInputDataTransformer
    ): void {
        $this->beConstructedWith(
            $objectNormalizer,
            $itemIriToIdentifierConverter,
            $commandAwareInputDataTransformer,
        );
    }

    function it_supports_denormalization_add_product_review(): void {
        $context['input']['class'] = AddProductReview::class;

        $this
            ->supportsDenormalization(
                new AddProductReview('Cap', 5, 'ok', 'cap_code', 'john@example.com'),
                AddProductReview::class,
                null,
                $context
            )
            ->shouldReturn(true)
        ;
    }

    function it_does_not_support_denormalization_for_not_supported_class(): void {
        $context['input']['class'] = Order::class;

        $this
            ->supportsDenormalization(
                new Order(),
                AddProductReview::class,
                null,
                $context
            )
            ->shouldReturn(false)
        ;
    }

    function it_denormalizes_add_product_review_and_transforms_product_field_from_iri_to_code(
        DenormalizerInterface $objectNormalizer,
        ItemIriToIdentifierConverterInterface $itemIriToIdentifierConverter,
        DataTransformerInterface $commandAwareInputDataTransformer
    ): void {
        $context['input']['class'] = AddProductReview::class;

        $addProductReview = new AddProductReview('Cap', 5, 'ok', 'cap_code', 'john@example.com');

        $itemIriToIdentifierConverter->isIdentifier('Cap')->willReturn(false);
        $itemIriToIdentifierConverter->isIdentifier(5)->willReturn(false);
        $itemIriToIdentifierConverter->isIdentifier('ok')->willReturn(false);
        $itemIriToIdentifierConverter->isIdentifier('john@example.com')->willReturn(false);
        $itemIriToIdentifierConverter->isIdentifier('/api/v2/shop/products/cap_code')->willReturn(true);
        $itemIriToIdentifierConverter->getIdentifier('/api/v2/shop/products/cap_code')->willReturn('cap_code');

        $objectNormalizer
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
                $context
            )
            ->willReturn($addProductReview)
        ;

        $commandAwareInputDataTransformer
            ->supportsTransformation($addProductReview, AddProductReview::class, $context)
            ->willReturn(false)
        ;
        $commandAwareInputDataTransformer
            ->transform($addProductReview, AddProductReview::class, $context)
            ->shouldNotBeCalled()
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
                $context
            )
            ->shouldReturn($addProductReview)
        ;
    }
}
