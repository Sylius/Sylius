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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ChannelBasedExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_does_nothing_if_current_resource_is_not_an_order(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ResourceInterface::class, new Get());
    }

    function it_does_nothing_when_user_is_an_admin(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        AdminUserInterface $adminUser,
    ): void {
        $userContext->getUser()->willReturn($adminUser);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, OrderInterface::class, new Get());
    }

    function it_throws_an_exception_if_context_has_no_channel_for_shop_user(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopUserInterface $shopUser,
    ): void {
        $userContext->getUser()->willReturn($shopUser);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('applyToCollection', [$queryBuilder, $queryNameGenerator, OrderInterface::class, new Get()])
        ;
    }

    function it_filters_orders_for_current_channel(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopUserInterface $shopUser,
        ChannelInterface $channel,
    ): void {
        $userContext->getUser()->willReturn($shopUser);

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryNameGenerator->generateParameterName('channel')->willReturn('channel');

        $queryBuilder->andWhere('o.channel = :channel')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('channel', $channel)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            new Get(),
            [ContextKeys::CHANNEL => $channel],
        );
    }

    function it_throws_an_access_denied_exception_if_user_is_not_recognised(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($user);

        $this
            ->shouldThrow(AccessDeniedException::class)
            ->during(
                'applyToCollection',
                [
                    $queryBuilder,
                    $queryNameGenerator,
                    OrderInterface::class,
                    new Get(),
                ],
            )
        ;
    }
}
