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
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class ChannelBasedExtensionSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    public function it_does_not_apply_conditions_to_collection_for_unsupported_resource(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $this->applyToCollection($queryBuilder, $queryNameGenerator, \stdClass::class);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    function it_does_not_apply_conditions_to_collection_for_admin_api_section(
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, AddressInterface::class);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    function it_throws_an_exception_if_context_has_not_channel(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('applyToCollection', [$queryBuilder, $queryNameGenerator, TaxonInterface::class, new Get()])
        ;
    }

    function it_applies_conditions_for_shop_api_section(
        TaxonRepositoryInterface $taxonRepository,
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        TaxonInterface $menuTaxon,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        ChannelInterface $channel,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

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
}
