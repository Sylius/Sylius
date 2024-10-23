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
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class EnabledChildrenExtensionSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    public function it_does_not_apply_conditions_to_item_for_unsupported_resource(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $this->applyToItem($queryBuilder, $queryNameGenerator, \stdClass::class, []);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    function it_does_not_apply_conditions_to_item_for_admin_api_section(
        SectionProviderInterface $sectionProvider,
        AdminApiSection $adminApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($adminApiSection);

        $this->applyToItem($queryBuilder, $queryNameGenerator, AddressInterface::class, []);

        $queryBuilder->getRootAliases()->shouldNotHaveBeenCalled();
        $queryBuilder->andWhere()->shouldNotHaveBeenCalled();
    }

    function it_applies_extension_to_item_query(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);
        $queryBuilder->getRootAliases()->willReturn(['rootAlias']);
        $queryNameGenerator->generateParameterName('enabled')->willReturn('enabled');
        $queryNameGenerator->generateJoinAlias('child')->willReturn('childAlias');

        $queryBuilder->addSelect('childAlias')->shouldBeCalled();
        $queryBuilder->leftJoin('rootAlias.children', 'childAlias', 'WITH', 'childAlias.enabled = :enabled')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('enabled', true)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToItem($queryBuilder, $queryNameGenerator, TaxonInterface::class, [], null, []);
    }
}
