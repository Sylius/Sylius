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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;

final class ProductItemDataProviderSpec extends ObjectBehavior
{
    function let(ProductRepositoryInterface $productRepository, UserContextInterface $userContext): void
    {
        $this->beConstructedWith($productRepository, $userContext);
    }

    function it_supports_only_product(): void
    {
        $this->supports(ProductInterface::class, Request::METHOD_GET)->shouldReturn(true);
        $this->supports(TaxonInterface::class, Request::METHOD_GET)->shouldReturn(false);
    }

    function it_throws_an_exception_if_context_has_no_channel(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getItem', [ProductInterface::class, 'ford', Request::METHOD_GET, []])
        ;
    }

    function it_provides_product_by_code_for_a_logged_in_admin_user(
        ProductRepositoryInterface $productRepository,
        UserContextInterface $userContext,
        AdminUserInterface $user,
        ProductInterface $product,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $productRepository->findOneByCode('FORD_FOCUS')->willReturn($product);

        $this
            ->getItem(ProductInterface::class, 'FORD_FOCUS', Request::METHOD_GET, [])
            ->shouldReturn($product)
        ;
    }

    function it_provides_product_by_slug_for_a_logged_in_shop_user(
        ProductRepositoryInterface $productRepository,
        UserContextInterface $userContext,
        UserInterface $user,
        ChannelInterface $channel,
        ProductInterface $product,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);

        $productRepository->findOneByChannelAndCodeWithAvailableAssociations($channel, 'FORD_FOCUS')->willReturn($product);

        $this
            ->getItem(
                ProductInterface::class,
                'FORD_FOCUS',
                Request::METHOD_GET,
                [
                    ContextKeys::CHANNEL => $channel,
                ],
            )
            ->shouldReturn($product)
        ;
    }

    function it_provides_product_by_slug_if_there_is_no_logged_in_user(
        ProductRepositoryInterface $productRepository,
        UserContextInterface $userContext,
        ChannelInterface $channel,
        ProductInterface $product,
    ): void {
        $userContext->getUser()->willReturn(null);

        $productRepository->findOneByChannelAndCodeWithAvailableAssociations($channel, 'FORD_FOCUS')->willReturn($product);

        $this
            ->getItem(
                ProductInterface::class,
                'FORD_FOCUS',
                Request::METHOD_GET,
                [
                    ContextKeys::CHANNEL => $channel,
                ],
            )
            ->shouldReturn($product)
        ;
    }
}
