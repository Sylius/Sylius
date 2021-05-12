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

use ApiPlatform\Core\Api\IriConverterInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\AddProductReview;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandAwareInputDataTransformer;
use Sylius\Bundle\ApiBundle\DataTransformer\LoggedInShopUserEmailAwareCommandDataTransformer;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class AddProductReviewCommandFieldItemIriToIdentifierDenormalizerSpec extends ObjectBehavior
{
    function let(
        DenormalizerInterface $objectNormalizer,
        IriConverterInterface $iriConverter,
        UserContextInterface $userContext
    ): void {
        $commandAwareInputDataTransformer = new CommandAwareInputDataTransformer(
            new LoggedInShopUserEmailAwareCommandDataTransformer(
                $userContext->getWrappedObject()
            )
        );

        $this->beConstructedWith($objectNormalizer, $commandAwareInputDataTransformer, $iriConverter);
    }

    function it_supports_denormalization_add_product_review(): void
    {
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

    function it_does_not_support_denormalization_other_than_add_product_review(): void
    {
        $context['input']['class'] = PickupCart::class;

        $this
            ->supportsDenormalization(
                new PickupCart(),
                AddProductReview::class,
                null,
                $context
            )
            ->shouldReturn(false)
        ;
    }

    function it_denormalizes_add_product_review_and_transforms_product_field_from_iri_to_code(
        DenormalizerInterface $objectNormalizer,
        IriConverterInterface $iriConverter,
        ProductInterface $product
    ): void {
        $context['input']['class'] = AddProductReview::class;

        $addProductReview = new AddProductReview('Cap', 5, 'ok', 'cap_code', 'john@example.com');

        $iriConverter->getItemFromIri('/api/v2/shop/products/cap_code')->willReturn($product);

        $product->getCode()->willReturn('cap_code');

        $objectNormalizer
            ->denormalize([
                'title' => 'Cap',
                'rating' => 5,
                'comment' => 'ok',
                'product' => 'cap_code',
                'email' => 'john@example.com'
            ],
                AddProductReview::class,
                null,
                $context
            )
            ->willReturn($addProductReview)
        ;

        $this
            ->denormalize([
                'title' => 'Cap',
                'rating' => 5,
                'comment' => 'ok',
                'product' => '/api/v2/shop/products/cap_code',
                'email' => 'john@example.com'
            ],
                AddProductReview::class,
                null,
                $context
            )
            ->shouldReturn($addProductReview)
        ;
    }
}
