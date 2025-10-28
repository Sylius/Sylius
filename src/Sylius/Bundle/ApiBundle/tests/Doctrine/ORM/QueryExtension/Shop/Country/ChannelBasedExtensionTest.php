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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Country;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Country\ChannelBasedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Request;

final class ChannelBasedExtensionTest extends TestCase
{
    private ChannelBasedExtension $extension;

    private MockObject&SectionProviderInterface $sectionProvider;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->extension = new ChannelBasedExtension($this->sectionProvider);
    }

    public function test_it_does_not_apply_conditions_to_collection_for_unsupported_resource(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($queryBuilder, $queryNameGenerator, \stdClass::class);
    }

    public function test_it_does_not_apply_conditions_to_collection_for_admin_api_section(): void
    {
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(AdminApiSection::class));
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $queryBuilder->expects($this->never())->method('getRootAliases');
        $queryBuilder->expects($this->never())->method('andWhere');

        $this->extension->applyToCollection($queryBuilder, $queryNameGenerator, CountryInterface::class);
    }

    public function test_it_throws_an_exception_if_context_has_not_channel(): void
    {
        $this->sectionProvider->method('getSection')->willReturn($this->createMock(ShopApiSection::class));

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->expectException(InvalidArgumentException::class);

        $this->extension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            CountryInterface::class,
            new Get(),
        );
    }

    public function test_it_applies_conditions_for_non_admin(): void
    {
        $shopApiSection = $this->createMock(ShopApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($shopApiSection);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $country = $this->createMock(CountryInterface::class);

        $countries = new ArrayCollection([$country]);

        $queryBuilder->method('getRootAliases')->willReturn(['o']);
        $channel->method('getEnabledCountries')->willReturn($countries);
        $queryNameGenerator->expects($this->once())->method('generateParameterName')->with('countries')->willReturn('countries');

        $queryBuilder->expects($this->once())->method('andWhere')->with('o.id in (:countries)')->willReturnSelf();
        $queryBuilder->expects($this->once())->method('setParameter')->with('countries', $countries)->willReturnSelf();

        $this->extension->applyToCollection(
            $queryBuilder,
            $queryNameGenerator,
            CountryInterface::class,
            new Get(name: Request::METHOD_GET),
            [
                ContextKeys::CHANNEL => $channel,
                ContextKeys::HTTP_REQUEST_METHOD_TYPE => Request::METHOD_GET,
            ],
        );
    }
}
