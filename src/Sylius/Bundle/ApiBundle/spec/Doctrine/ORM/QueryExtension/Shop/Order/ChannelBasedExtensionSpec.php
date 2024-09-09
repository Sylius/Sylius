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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class ChannelBasedExtensionSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    function it_does_nothing_if_current_resource_is_not_an_order(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ResourceInterface::class, new Get());
    }

    function it_does_nothing_when_section_is_not_shop_api(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        AdminApiSection $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);

        $this->applyToCollection($queryBuilder, $queryNameGenerator, OrderInterface::class, new Get());
    }

    function it_throws_an_exception_if_context_has_no_channel_for_shop_user(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopApiSection $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('applyToCollection', [$queryBuilder, $queryNameGenerator, OrderInterface::class, new Get()])
        ;
    }

    function it_filters_orders_for_current_channel(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ShopApiSection $section,
        ChannelInterface $channel,
    ): void {
        $sectionProvider->getSection()->willReturn($section);

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryNameGenerator->generateParameterName('channel')->willReturn('channel');

        $queryBuilder->andWhere('o.channel = :channel')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('channel', $channel)->shouldBeCalled()->willReturn($queryBuilder);

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            OrderInterface::class,
            new Get(),
            [ContextKeys::CHANNEL => $channel],
        );
    }
}
