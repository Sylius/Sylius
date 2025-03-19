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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductAssociation;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Symfony\Component\HttpFoundation\Request;

final class EnabledProductsExtensionSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    function it_does_nothing_if_current_resource_is_not_a_product_association(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->shouldNotBeCalled();
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            ProductVariantInterface::class,
            [],
            new Get(),
        );
    }

    function it_does_nothing_if_section_is_not_shop_api(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        AdminApiSection $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);

        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            ProductAssociationInterface::class,
            [],
            new Get(),
        );
    }

    function it_applies_conditions_for_customer(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
        ShopApiSection $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);

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
