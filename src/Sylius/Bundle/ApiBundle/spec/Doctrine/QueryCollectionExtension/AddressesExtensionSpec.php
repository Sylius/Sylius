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
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class AddressesExtensionSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext): void
    {
        $this->beConstructedWith($userContext);
    }

    function it_applies_conditions_to_get_addresses_for_logged_in_shop_user(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
    ): void {
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $queryNameGenerator->generateParameterName('customer')->shouldBeCalled()->willReturn('customer');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->innerJoin('o.customer', 'customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('o.customer = :customer')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('customer', $customer)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            AddressInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    function it_does_not_apply_conditions_to_get_addresses_for_logged_in_admin_user(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        AdminUserInterface $adminUser,
    ): void {
        $userContext->getUser()->willReturn($adminUser);
        $adminUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            AddressInterface::class,
            new Get(name: Request::METHOD_GET),
        );
    }

    function it_throws_an_exception_if_there_is_not_logged_in_user(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn(null);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this
            ->shouldThrow(MissingTokenException::class)
            ->during(
                'applyToCollection',
                [
                    $queryBuilder,
                    $queryNameGenerator,
                    AddressInterface::class,
                    new Get(name: Request::METHOD_GET),
                ],
            )
        ;
    }

    function it_throws_an_exception_if_there_is_logged_in_admin_user_without_proper_role(
        UserContextInterface $userContext,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        AdminUserInterface $adminUser,
    ): void {
        $userContext->getUser()->willReturn($adminUser);
        $adminUser->getRoles()->willReturn([]);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this
            ->shouldThrow(AccessDeniedHttpException::class)
            ->during(
                'applyToCollection',
                [
                    $queryBuilder,
                    $queryNameGenerator,
                    AddressInterface::class,
                    new Get(name: Request::METHOD_GET),
                ],
            )
        ;
    }
}
