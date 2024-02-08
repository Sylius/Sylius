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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRate;
use Symfony\Component\HttpFoundation\Request;

final class ExchangeRateExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_throws_an_exception_if_context_has_not_channel(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('applyToCollection', [$queryBuilder, $queryNameGenerator, ExchangeRate::class, 'get', []])
        ;
    }

    function it_does_not_apply_conditions_to_collection_for_admin(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        AdminUserInterface $admin,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
    ): void {
        $userContext->getUser()->willReturn($admin);
        $admin->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            ExchangeRate::class,
            Request::METHOD_GET,
            [
                ContextKeys::CHANNEL => $channel->getWrappedObject(),
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }

    function it_does_not_apply_conditions_to_item_for_admin(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        AdminUserInterface $admin,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
    ): void {
        $userContext->getUser()->willReturn($admin);
        $admin->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            ExchangeRate::class,
            [],
            Request::METHOD_GET,
            [
                ContextKeys::CHANNEL => $channel->getWrappedObject(),
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }

    function it_applies_conditions_to_collection_for_non_admin(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        Expr $expr,
        Expr\Orx $exprOrx,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn(null);

        $queryBuilder->expr()->willReturn($expr);
        $expr->orX('o.sourceCurrency = :currency', 'o.targetCurrency = :currency')->willReturn($exprOrx);

        $queryBuilder->andWhere($exprOrx)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $channel->getBaseCurrency()->shouldBeCalled()->willReturn($currency);

        $queryNameGenerator->generateParameterName('currency')->shouldBeCalled()->willReturn('currency');

        $queryBuilder->setParameter('currency', $currency)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            ExchangeRate::class,
            Request::METHOD_GET,
            [
                ContextKeys::CHANNEL => $channel->getWrappedObject(),
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }

    function it_applies_conditions_to_item_for_non_admin(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
        CurrencyInterface $currency,
        Expr $expr,
        Expr\Orx $exprOrx,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn(null);

        $queryBuilder->expr()->willReturn($expr);
        $expr->orX('o.sourceCurrency = :currency', 'o.targetCurrency = :currency')->willReturn($exprOrx);

        $queryBuilder->andWhere($exprOrx)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $channel->getBaseCurrency()->shouldBeCalled()->willReturn($currency);

        $queryNameGenerator->generateParameterName('currency')->shouldBeCalled()->willReturn('currency');

        $queryBuilder->setParameter('currency', $currency)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            ExchangeRate::class,
            [],
            Request::METHOD_GET,
            [
                ContextKeys::CHANNEL => $channel->getWrappedObject(),
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }
}
