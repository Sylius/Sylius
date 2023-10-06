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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class EnabledProductVariantsExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext)
    {
        $this->beConstructedWith($userContext);
    }

    function it_does_nothing_if_current_resource_is_not_a_product_variant(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, TaxonInterface::class, new Get());
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

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductVariantInterface::class, new Get());
    }

    function it_filters_products_by_enabled_flag_for_shop_user(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);

        $queryNameGenerator->generateParameterName('enabled')->shouldBeCalled()->willReturn('enabled');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->andWhere('o.enabled = :enabled')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('enabled', true)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            ProductVariantInterface::class,
            new Get(),
            [ContextKeys::CHANNEL => $channel, ContextKeys::LOCALE_CODE => 'en_US'],
        );
    }
}
