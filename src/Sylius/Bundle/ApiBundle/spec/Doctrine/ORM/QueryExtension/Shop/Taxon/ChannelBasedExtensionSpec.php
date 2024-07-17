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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Taxon;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class ChannelBasedExtensionSpec extends ObjectBehavior
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
            ->during('applyToCollection', [$queryBuilder, $queryNameGenerator, TaxonInterface::class, new Get()])
        ;
    }

    function it_applies_conditions_if_logged_in_user_is_not_admin(
        TaxonRepositoryInterface $taxonRepository,
        UserContextInterface $userContext,
        TaxonInterface $menuTaxon,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        ChannelInterface $channel,
        ShopUserInterface $user,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn($user);

        $channel->getMenuTaxon()->willReturn($menuTaxon);

        $menuTaxon->getCode()->willReturn('code');

        $queryNameGenerator->generateParameterName('parentCode')->shouldBeCalled()->willReturn('parentCode');
        $queryNameGenerator->generateParameterName('enabled')->shouldBeCalled()->willReturn('enabled');

        $queryBuilder->getRootAliases()->shouldBeCalled()->willReturn('o');
        $queryBuilder->addSelect('child')->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->innerJoin('o.parent', 'parent')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->leftJoin('o.children', 'child', 'WITH', 'child.enabled = true')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('o.enabled = :enabled')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->andWhere('parent.code = :parentCode')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->addOrderBy('o.position')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('parentCode', 'code')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('enabled', true)->willReturn($queryBuilder->getWrappedObject());

        $taxonRepository->findChildrenByChannelMenuTaxon($menuTaxon)->willReturn([$firstTaxon, $secondTaxon]);

        $this->applyToCollection(
            $queryBuilder->getWrappedObject(),
            $queryNameGenerator->getWrappedObject(),
            TaxonInterface::class,
            new Get(),
            [
                ContextKeys::CHANNEL => $channel->getWrappedObject(),
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }

    function it_applies_conditions_if_there_is_no_logged_in_user(
        TaxonRepositoryInterface $taxonRepository,
        UserContextInterface $userContext,
        TaxonInterface $menuTaxon,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        ChannelInterface $channel,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $userContext->getUser()->willReturn(null);

        $channel->getMenuTaxon()->willReturn($menuTaxon);

        $menuTaxon->getCode()->willReturn('code');

        $queryNameGenerator->generateParameterName('parentCode')->shouldBeCalled()->willReturn('parentCode');
        $queryNameGenerator->generateParameterName('enabled')->shouldBeCalled()->willReturn('enabled');

        $queryBuilder->getRootAliases()->shouldBeCalled()->willReturn('o');
        $queryBuilder->addSelect('child')->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->innerJoin('o.parent', 'parent')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->leftJoin('o.children', 'child', 'WITH', 'child.enabled = true')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->andWhere('o.enabled = :enabled')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->andWhere('parent.code = :parentCode')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->addOrderBy('o.position')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('parentCode', 'code')->willReturn($queryBuilder->getWrappedObject());
        $queryBuilder->setParameter('enabled', true)->willReturn($queryBuilder->getWrappedObject());

        $taxonRepository->findChildrenByChannelMenuTaxon($menuTaxon)->willReturn([$firstTaxon, $secondTaxon]);

        $this->applyToCollection(
            $queryBuilder->getWrappedObject(),
            $queryNameGenerator->getWrappedObject(),
            TaxonInterface::class,
            new Get(),
            [
                ContextKeys::CHANNEL => $channel->getWrappedObject(),
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }

    function it_does_not_apply_conditions_if_logged_in_user_is_admin(
        UserContextInterface $userContext,
        ChannelInterface $channel,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        AdminUserInterface $admin,
    ): void {
        $userContext->getUser()->willReturn($admin);

        $admin->getRoles()->shouldBeCalled()->willReturn(['ROLE_API_ACCESS']);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->addSelect('child')->shouldNotBeCalled();
        $queryBuilder->innerJoin('o.parent', 'parent')->shouldNotBeCalled();
        $queryBuilder->leftJoin('o.children', 'child', 'WITH', 'child.enabled = true')->shouldNotBeCalled();
        $queryBuilder->andWhere('o.enabled = :enabled')->shouldNotBeCalled();
        $queryBuilder->andWhere('parent.code = :parentCode')->shouldNotBeCalled();
        $queryBuilder->addOrderBy('o.position')->shouldNotBeCalled();
        $queryBuilder->setParameter('parentCode', 'code')->shouldNotBeCalled();
        $queryBuilder->setParameter('enabled', true)->shouldNotBeCalled();

        $this->applyToCollection(
            $queryBuilder->getWrappedObject(),
            $queryNameGenerator->getWrappedObject(),
            TaxonInterface::class,
            new Get(),
            [
                ContextKeys::CHANNEL => $channel->getWrappedObject(),
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }
}
