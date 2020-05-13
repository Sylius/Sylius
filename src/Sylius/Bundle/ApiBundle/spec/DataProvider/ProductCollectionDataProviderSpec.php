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

use function Amp\Promise\first;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductCollectionDataProviderSpec extends ObjectBehavior
{
    function let(
        ProductRepositoryInterface $productRepository,
        UserContextInterface $userContext
    ): void {
        $this->beConstructedWith($productRepository, $userContext, []);
    }

    function it_supports_only_operations_on_product_entity(): void
    {
        $this->supports(ProductInterface::class, 'get')->shouldReturn(true);
        $this->supports(TaxonInterface::class, 'get')->shouldReturn(false);
    }

    function it_throws_an_exception_if_context_has_no_channel_for_shop_user(
        UserContextInterface $userContext,
        UserInterface $user
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getCollection', [ProductInterface::class, 'get', []])
        ;
    }

    function it_throws_an_exception_if_context_has_no_locale_for_shop_user(
        UserContextInterface $userContext,
        UserInterface $user,
        ChannelInterface $channel
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getCollection', [ProductInterface::class, 'get', [ContextKeys::CHANNEL => $channel]])
        ;
    }

    function it_provides_products_for_admin_user(
        EntityRepository $productRepository,
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        AbstractQuery $query,
        ProductInterface $firstProduct,
        ProductInterface $secondProduct
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);
        $productRepository->createQueryBuilder('o')->willReturn($queryBuilder);
        $queryBuilder->getQuery()->willReturn($query);
        $query->getResult()->willReturn([$firstProduct, $secondProduct]);

        $this
            ->getCollection(ProductInterface::class, 'get', [])
            ->shouldReturn([$firstProduct, $secondProduct])
        ;
    }

    function it_provides_products_for_shop_user(
        ProductRepositoryInterface $productRepository,
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        AbstractQuery $query,
        ChannelInterface $channel,
        ProductInterface $firstProduct,
        ProductInterface $secondProduct
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);
        $productRepository
            ->createListQueryBuilderByChannelAndLocaleCode($channel, 'en_US')
            ->willReturn($queryBuilder)
        ;
        $queryBuilder->getQuery()->willReturn($query);
        $query->getResult()->willReturn([$firstProduct, $secondProduct]);

        $this
            ->getCollection(
                ProductInterface::class,
                'get',
                [ContextKeys::CHANNEL => $channel, ContextKeys::LOCALE_CODE => 'en_US']
            )
            ->shouldReturn([$firstProduct, $secondProduct])
        ;
    }
}
