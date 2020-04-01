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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

class LatestProductCollectionDataProviderSpec extends ObjectBehavior
{
    function let(ProductRepositoryInterface $productRepository): void
    {
        $this->beConstructedWith($productRepository);
    }

    function it_supports_only_get_latest_operation_on_product_entity(): void
    {
        $this->supports(ProductInterface::class, 'get_latest')->shouldReturn(true);
        $this->supports(TaxonInterface::class, 'get_latest')->shouldReturn(false);
    }

    function it_throws_an_exception_if_context_has_not_channel(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getCollection', [ProductInterface::class, 'get_latest', []])
        ;
    }

    function it_throws_an_exception_if_context_has_not_locale(ChannelInterface $channel): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getCollection', [ProductInterface::class, 'get_latest', [ContextKeys::CHANNEL => $channel]])
        ;
    }

    function it_provides_latest_product_from_channel_in_locale(
        ProductRepositoryInterface $productRepository,
        ChannelInterface $channel,
        ProductInterface $firstProduct,
        ProductInterface $secondProduct
    ): void {
        $productRepository->findLatestByChannel($channel, 'en_US', 3)->willReturn([$firstProduct, $secondProduct]);

        $this
            ->getCollection(
                ProductInterface::class,
                'get_latest',
                [ContextKeys::CHANNEL => $channel, ContextKeys::LOCALE_CODE => 'en_US'])
            ->shouldReturn([$firstProduct, $secondProduct])
        ;
    }
}
