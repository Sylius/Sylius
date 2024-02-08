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

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;

final class AddressItemExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_does_not_apply_conditions_for_admin(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        AdminUserInterface $admin,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn($admin);
        $admin->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->innerJoin('o.customer', 'customer')->shouldNotBeCalled();
        $queryBuilder->andWhere('customer = :customer')->shouldNotBeCalled();

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            AddressInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            Request::METHOD_GET,
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET],
        );
    }

    function it_applies_conditions_for_customer(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryNameGenerator->generateParameterName('customer')->shouldBeCalled()->willReturn('customer');
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);
        $shopUser->getCustomer()->willReturn($customer);

        $queryBuilder->innerJoin('o.customer', 'customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('customer = :customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('customer', $customer)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            AddressInterface::class,
            ['tokenValue' => 'xaza-tt_fee'],
            Request::METHOD_GET,
            [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET],
        );
    }

    function it_throws_an_exception_anonymous_user_tries_to_get_address(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $userContext->getUser()->willReturn(null);

        $this
            ->shouldThrow(MissingTokenException::class)
            ->during(
                'applyToItem',
                [
                    $queryBuilder,
                    $queryNameGenerator,
                    AddressInterface::class,
                    ['tokenValue' => 'xaza-tt_fee'],
                    Request::METHOD_GET,
                    [ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET],
                ],
            )
        ;
    }
}
