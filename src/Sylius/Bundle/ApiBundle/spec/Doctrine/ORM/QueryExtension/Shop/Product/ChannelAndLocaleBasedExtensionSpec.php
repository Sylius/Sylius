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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product;

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
use Sylius\Component\Core\Model\ProductInterface;

final class ChannelAndLocaleBasedExtensionSpec extends ObjectBehavior
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

    function it_throws_an_exception_if_context_has_no_channel_for_shop_user(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('applyToCollection', [$queryBuilder, $queryNameGenerator, ProductInterface::class, new Get()])
        ;
    }

    function it_throws_an_exception_if_context_has_no_locale_for_shop_user(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during(
                'applyToCollection',
                [$queryBuilder, $queryNameGenerator, ProductInterface::class, new Get(), [ContextKeys::CHANNEL => $channel]],
            )
        ;
    }

    function it_filters_products_by_channel_and_locale_code_for_shop_user(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
    ): void {
        $sectionProvider->getSection()->willReturn($shopApiSection);

        $queryNameGenerator->generateParameterName('channel')->shouldBeCalled()->willReturn('channel');
        $queryNameGenerator->generateParameterName('localeCode')->shouldBeCalled()->willReturn('localeCode');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->addSelect('translation')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :localeCode')
            ->shouldBeCalled()
            ->willReturn($queryBuilder)
        ;
        $queryBuilder->andWhere(':channel MEMBER OF o.channels')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('channel', $channel)->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('localeCode', 'en_US')->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            ProductInterface::class,
            new Get(),
            [ContextKeys::CHANNEL => $channel, ContextKeys::LOCALE_CODE => 'en_US'],
        );
    }
}
