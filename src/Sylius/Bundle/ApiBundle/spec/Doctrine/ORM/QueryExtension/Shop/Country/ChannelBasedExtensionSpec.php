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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Country;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
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

        $this->applyToCollection($queryBuilder, $queryNameGenerator, CountryInterface::class);

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
            ->during('applyToCollection', [$queryBuilder, $queryNameGenerator, CountryInterface::class, new Get()])
        ;
    }

    function it_applies_conditions_for_non_admin(
        SectionProviderInterface $sectionProvider,
        ShopApiSection $shopApiSection,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        ChannelInterface $channel,
        CountryInterface $country,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $sectionProvider->getSection()->willReturn($shopApiSection);

        $queryBuilder->andWhere('o.id in (:countries)')->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $countriesCollection = new ArrayCollection([$country]);

        $channel->getCountries()->shouldBeCalled()->willReturn($countriesCollection);

        $queryNameGenerator->generateParameterName('countries')->shouldBeCalled()->willReturn('countries');

        $queryBuilder->setParameter('countries', $countriesCollection)->shouldBeCalled()->willReturn($queryBuilder->getWrappedObject());

        $this->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            CountryInterface::class,
            new Get(name: Request::METHOD_GET),
            [
                ContextKeys::CHANNEL => $channel->getWrappedObject(),
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }
}
