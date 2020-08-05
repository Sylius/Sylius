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
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Helper\UserContextHelperInterface;
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
    function let(ProductRepositoryInterface $productRepository, UserContextHelperInterface $userContextHelper): void
    {
        $this->beConstructedWith($productRepository, $userContextHelper);
    }

    function it_supports_only_product(): void
    {
        $this->supports(ProductInterface::class, Request::METHOD_GET)->shouldReturn(true);
        $this->supports(TaxonInterface::class, Request::METHOD_GET)->shouldReturn(false);
    }

    function it_throws_an_exception_if_context_has_no_channel(UserContextHelperInterface $userContextHelper): void
    {
        $userContextHelper->hasAdminRoleApiAccess()->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getItem', [
                ProductInterface::class,
                'ford',
                Request::METHOD_GET,
                [ContextKeys::LOCALE_CODE => 'en_US'],
            ])
        ;
    }

    function it_throws_an_exception_if_context_has_no_locale_code(
        ChannelInterface $channel,
        UserContextHelperInterface $userContextHelper
    ): void {
        $userContextHelper->hasAdminRoleApiAccess()->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getItem', [
                ProductInterface::class,
                'ford',
                Request::METHOD_GET,
                [ContextKeys::CHANNEL => $channel],
            ])
        ;
    }

    function it_provides_product_by_code_for_a_logged_in_admin_user(
        ProductRepositoryInterface $productRepository,
        UserContextHelperInterface $userContextHelper,
        AdminUserInterface $user,
        ProductInterface $product
    ): void {
        $userContextHelper->hasAdminRoleApiAccess()->willReturn(true);

        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $productRepository->findOneByCode('FORD_FOCUS')->willReturn($product);

        $this
            ->getItem(
                ProductInterface::class,
                'FORD_FOCUS',
                Request::METHOD_GET,
                []
            )
            ->shouldReturn($product)
        ;
    }

    function it_provides_product_by_slug_for_a_logged_in_shop_user(
        ProductRepositoryInterface $productRepository,
        UserContextHelperInterface $userContextHelper,
        UserInterface $user,
        ChannelInterface $channel,
        ProductInterface $product
    ): void {
        $userContextHelper->hasAdminRoleApiAccess()->willReturn(false);
        $user->getRoles()->willReturn([]);

        $productRepository->findOneByChannelAndSlug($channel, 'en_US','FORD_FOCUS')->willReturn($product);

        $this
            ->getItem(
                ProductInterface::class,
                'FORD_FOCUS',
                Request::METHOD_GET,
                [
                    ContextKeys::CHANNEL => $channel,
                    ContextKeys::LOCALE_CODE => 'en_US',
                ]
            )
            ->shouldReturn($product)
        ;
    }

    function it_provides_product_by_slug_if_there_is_no_logged_in_user(
        ProductRepositoryInterface $productRepository,
        UserContextHelperInterface $userContextHelper,
        ChannelInterface $channel,
        ProductInterface $product
    ): void {
        $userContextHelper->hasAdminRoleApiAccess()->willReturn(false);

        $productRepository->findOneByChannelAndSlug($channel, 'en_US','FORD_FOCUS')->willReturn($product);

        $this
            ->getItem(
                ProductInterface::class,
                'FORD_FOCUS',
                Request::METHOD_GET,
                [
                    ContextKeys::CHANNEL => $channel,
                    ContextKeys::LOCALE_CODE => 'en_US'
                ]
            )
            ->shouldReturn($product)
        ;
    }
}
