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

namespace Sylius\Bundle\ApiBundle\Tests\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\ApiBundle\Serializer\ProductVariantNormalizer;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. */
final class ProductVariantNormalizerTest extends TestCase
{
    private ProductVariantPricesCalculatorInterface&MockObject $pricesCalculator;
    private AvailabilityCheckerInterface&MockObject $availabilityChecker;
    private SectionProviderInterface&MockObject $sectionProvider;
    private IriConverterInterface&MockObject $iriConverter;
    private NormalizerInterface&MockObject $normalizer;

    protected function setUp(): void
    {
        $this->pricesCalculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);
    }

    public function testSerializesProductVariantIfItemOperationNameIsDifferentThanAdminGetWithoutLowestPrice(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $variant = $this->createMock(ProductVariantInterface::class);

        $this->normalizer->method('normalize')->willReturn([]);
        $this->pricesCalculator->method('calculate')->willReturn(1000);
        $this->pricesCalculator->method('calculateOriginal')->willReturn(1000);
        $variant->method('getAppliedPromotionsForChannel')->willReturn(new ArrayCollection());
        $this->availabilityChecker->method('isStockAvailable')->willReturn(true);

        $normalizer = new ProductVariantNormalizer($this->pricesCalculator, $this->availabilityChecker, $this->sectionProvider, $this->iriConverter);
        $normalizer->setNormalizer($this->normalizer);

        $result = $normalizer->normalize($variant, null, [ContextKeys::CHANNEL => $channel]);

        $this->assertEquals(['price' => 1000, 'originalPrice' => 1000, 'inStock' => true], $result);
    }
}
