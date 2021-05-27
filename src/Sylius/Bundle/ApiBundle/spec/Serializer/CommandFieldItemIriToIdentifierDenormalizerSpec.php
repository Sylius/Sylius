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

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\AddProductReview;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Converter\ItemIriToIdentifierConverterInterface;
use Sylius\Bundle\ApiBundle\DataTransformer\CommandAwareInputDataTransformer;
use Sylius\Bundle\ApiBundle\DataTransformer\LoggedInShopUserEmailAwareCommandDataTransformer;
use Sylius\Bundle\ApiBundle\Map\CommandItemIriArgumentToIdentifierMapInterface;
use Sylius\Component\Core\Model\Order;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CommandFieldItemIriToIdentifierDenormalizerSpec extends ObjectBehavior
{
    function let(
        DenormalizerInterface $objectNormalizer,
        ItemIriToIdentifierConverterInterface $itemIriToIdentifierConverter,
        CommandItemIriArgumentToIdentifierMapInterface $commandItemIriArgumentToIdentifierMap,
        UserContextInterface $userContext
    ): void {
        $commandAwareInputDataTransformer = new CommandAwareInputDataTransformer(
            new LoggedInShopUserEmailAwareCommandDataTransformer(
                $userContext->getWrappedObject()
            )
        );

        $this->beConstructedWith(
            $objectNormalizer,
            $itemIriToIdentifierConverter,
            $commandAwareInputDataTransformer,
            $commandItemIriArgumentToIdentifierMap
        );
    }

    function it_supports_denormalization_add_product_review(
        CommandItemIriArgumentToIdentifierMapInterface $commandItemIriArgumentToIdentifierMap
    ): void {
        $context['input']['class'] = AddProductReview::class;

        $commandItemIriArgumentToIdentifierMap->has(AddProductReview::class)->willReturn(true);

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

    function it_does_not_support_denormalization_for_not_supported_class(
        CommandItemIriArgumentToIdentifierMapInterface $commandItemIriArgumentToIdentifierMap
    ): void {
        $context['input']['class'] = Order::class;

        $commandItemIriArgumentToIdentifierMap->has(Order::class)->willReturn(false);

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
        CommandItemIriArgumentToIdentifierMapInterface $commandItemIriArgumentToIdentifierMap,
        UserContextInterface $userContext
    ): void {
        $context['input']['class'] = AddProductReview::class;

        $addProductReview = new AddProductReview('Cap', 5, 'ok', 'cap_code', 'john@example.com');

        $commandItemIriArgumentToIdentifierMap->get(AddProductReview::class)->willReturn('product');
        $commandItemIriArgumentToIdentifierMap->has(AddProductReview::class)->willReturn(true);

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
