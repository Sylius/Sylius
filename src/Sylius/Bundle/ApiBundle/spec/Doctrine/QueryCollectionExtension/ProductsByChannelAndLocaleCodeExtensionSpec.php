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

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class ProductsByChannelAndLocaleCodeExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_does_nothing_if_current_resource_is_not_a_product(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->addSelect('translation')->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, TaxonInterface::class, new Get());
    }

    function it_throws_an_exception_if_context_has_no_channel_for_shop_user(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('applyToCollection', [$queryBuilder, $queryNameGenerator, ProductInterface::class, new Get()])
        ;
    }

    function it_throws_an_exception_if_context_has_no_locale_for_shop_user(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during(
                'applyToCollection',
                [$queryBuilder, $queryNameGenerator, ProductInterface::class, new Get(), [ContextKeys::CHANNEL => $channel]],
            )
        ;
    }

    function it_does_nothing_if_current_user_is_an_admin_user(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->addSelect('translation')->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, new Get());
    }

    function it_filters_products_by_channel_and_locale_code_for_shop_user(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);

        $queryNameGenerator->generateParameterName('channel')->shouldBeCalled()->willReturn('channel');
        $queryNameGenerator->generateParameterName('localeCode')->shouldBeCalled()->willReturn('localeCode');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->addSelect('translation')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :localeCode')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;
        $queryBuilder->andWhere(':channel MEMBER OF o.channels')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('channel', $channel)->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('localeCode', 'en_US')->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            ProductInterface::class,
            new Get(),
            [ContextKeys::CHANNEL => $channel, ContextKeys::LOCALE_CODE => 'en_US'],
        );
    }
}
