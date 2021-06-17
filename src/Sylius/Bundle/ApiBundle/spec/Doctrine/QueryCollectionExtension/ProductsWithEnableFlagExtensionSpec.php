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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ProductsWithEnableFlagExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext)
    {
        $this->beConstructedWith($userContext);
    }

    function it_does_nothing_if_current_resource_is_not_a_product(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator
    ): void {
        $userContext->getUser()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, TaxonInterface::class, 'get', []);
    }

    function it_does_nothing_if_current_user_is_an_admin_user(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, 'get', []);
    }

    function it_filters_products_by_enabled_flag_for_shop_user(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->andWhere('o.enabled = :enabled')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('enabled', true)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, 'get', [ContextKeys::CHANNEL => $channel, ContextKeys::LOCALE_CODE => 'en_US']);
    }
}
