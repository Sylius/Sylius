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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Symfony\Component\HttpFoundation\Request;

final class CurrencyCollectionExtensionSpec extends ObjectBehavior
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
            ->during('applyToCollection', [$queryBuilder, $queryNameGenerator, CurrencyInterface::class, 'get', []])
        ;
    }

    function it_does_not_apply_conditions_for_admin(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        AdminUserInterface $admin,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn($admin);
        $admin->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->andWhere('o..id in :currencies')->shouldNotBeCalled();
        $queryBuilder->setParameter('currencies', Argument::any())->shouldNotBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            CurrencyInterface::class,
            Request::METHOD_GET,
            [
                ContextKeys::CHANNEL => $channel->getWrappedObject(),
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }

    function it_applies_conditions_for_non_admin(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
        CurrencyInterface $currency,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn(null);

        $queryBuilder->andWhere('o.id in (:currencies)')->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $currenciesCollection = new ArrayCollection([$currency]);

        $channel->getCurrencies()->shouldBeCalled()->willReturn($currenciesCollection);

        $queryNameGenerator->generateParameterName('currencies')->shouldBeCalled()->willReturn('currencies');

        $queryBuilder->setParameter('currencies', $currenciesCollection)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            CurrencyInterface::class,
            Request::METHOD_GET,
            [
                ContextKeys::CHANNEL => $channel->getWrappedObject(),
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }
}
