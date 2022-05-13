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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;

final class OrderGetMethodItemExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_applies_conditions_to_get_order_with_state_cart_by_authorized_shop_user_that_is_assigns_to_this_order(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        QueryNameGeneratorInterface $queryNameGenerator
    ): void {
        $queryNameGenerator->generateParameterName('customer')->shouldBeCalled()->willReturn('customer');
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $queryBuilder
            ->andWhere(sprintf('%s.customer = :customer', 'o'))
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;
        $queryBuilder
            ->setParameter('customer', 1)
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            Request::METHOD_GET,
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET],
        );
    }
}
