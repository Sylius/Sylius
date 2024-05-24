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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

final class EnabledProductInProductAssociationItemExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_does_nothing_if_current_resource_is_not_a_product_association(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            ProductVariantInterface::class,
            [],
            new Get(),
        );
    }

    function it_does_nothing_if_current_user_is_an_admin_user(
        UserContextInterface $userContext,
        AdminUserInterface $adminUser,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($adminUser);
        $adminUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            ProductAssociationInterface::class,
            [],
            new Get(),
        );
    }

    function it_applies_conditions_for_customer(
        UserContextInterface $userContext,
        UserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
    ): void {
        $userContext->getUser()->willReturn($user);
        $user->getRoles()->willReturn([]);

        $queryNameGenerator->generateParameterName('enabled')->shouldBeCalled()->willReturn('enabled');
        $queryNameGenerator->generateParameterName('channel')->shouldBeCalled()->willReturn('channel');
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $queryBuilder->addSelect('associatedProduct')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->leftJoin('o.associatedProducts', 'associatedProduct', 'WITH', 'associatedProduct.enabled = :enabled')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->innerJoin('associatedProduct.channels', 'channel', 'WITH', 'channel = :channel')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('enabled', true)->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('channel', $channel)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            ProductAssociationInterface::class,
            [],
            new Get(),
            [
                ContextKeys::CHANNEL => $channel->getWrappedObject(),
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }
}
