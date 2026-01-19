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

namespace Tests\Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use ApiPlatform\Metadata\IriConverterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ChannelPriceHistoryConfigDenormalizer;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ChannelPriceHistoryConfigDenormalizerTest extends TestCase
{
    private IriConverterInterface&MockObject $iriConverter;

    private FactoryInterface&MockObject $configFactory;

    private ChannelPriceHistoryConfigDenormalizer $channelPriceHistoryConfigDenormalizer;

    private const ALREADY_CALLED = 'sylius_channel_price_history_config_denormalizer_already_called';

    protected function setUp(): void
    {
        parent::setUp();
        $this->iriConverter = $this->createMock(IriConverterInterface::class);
        $this->configFactory = $this->createMock(FactoryInterface::class);
        $this->channelPriceHistoryConfigDenormalizer = new ChannelPriceHistoryConfigDenormalizer(
            $this->iriConverter,
            $this->configFactory,
        );
    }

    public function testDoesNotSupportDenormalizationWhenTheDenormalizerHasAlreadyBeenCalled(): void
    {
        self::assertFalse(
            $this->channelPriceHistoryConfigDenormalizer->supportsDenormalization(
                [],
                'string',
                context: [self::ALREADY_CALLED => true],
            ),
        );
    }

    public function testDoesNotSupportDenormalizationWhenDataIsNotAnArray(): void
    {
        self::assertFalse(
            $this->channelPriceHistoryConfigDenormalizer->supportsDenormalization('string', 'string'),
        );
    }

    public function testDoesNotSupportDenormalizationWhenTypeIsNotAChannelPriceHistoryConfig(): void
    {
        self::assertFalse($this->channelPriceHistoryConfigDenormalizer->supportsDenormalization([], 'string'));
    }

    public function testThrowsAnExceptionWhenDenormalizingAnObjectThatIsNotAChannelPriceHistoryConfig(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        $this->channelPriceHistoryConfigDenormalizer->setDenormalizer($denormalizerMock);

        $denormalizerMock
            ->expects(self::once())
            ->method('denormalize')
            ->with([], 'string', null, [self::ALREADY_CALLED => true])
            ->willReturn(new \stdClass());

        self::expectException(InvalidArgumentException::class);

        $this->channelPriceHistoryConfigDenormalizer->denormalize([], 'string');
    }

    public function testAddsExcludedTaxonsFromData(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var TaxonInterface|MockObject $firstTaxonMock */
        $firstTaxonMock = $this->createMock(TaxonInterface::class);
        /** @var TaxonInterface|MockObject $secondTaxonMock */
        $secondTaxonMock = $this->createMock(TaxonInterface::class);
        /** @var ChannelPriceHistoryConfigInterface|MockObject $configMock */
        $configMock = $this->createMock(ChannelPriceHistoryConfigInterface::class);

        $this->channelPriceHistoryConfigDenormalizer->setDenormalizer($denormalizerMock);

        $data = ['taxonsExcludedFromShowingLowestPrice' => [
            '/api/v2/taxons/first-new-taxon',
            '/api/v2/taxons/second-new-taxon',
        ]];

        $denormalizerMock
            ->expects(self::once())
            ->method('denormalize')
            ->with($data, 'string', null, [self::ALREADY_CALLED => true])
            ->willReturn($configMock);

        $configMock
            ->expects(self::once())
            ->method('clearTaxonsExcludedFromShowingLowestPrice');

        $this->iriConverter
            ->expects($this->exactly(2))
            ->method('getResourceFromIri')
            ->with($this->callback(function ($iri) {
                return in_array($iri, [
                    '/api/v2/taxons/first-new-taxon',
                    '/api/v2/taxons/second-new-taxon',
                ], true);
            }))
            ->willReturnCallback(function ($iri) use ($firstTaxonMock, $secondTaxonMock) {
                return $iri === '/api/v2/taxons/first-new-taxon' ? $firstTaxonMock : $secondTaxonMock;
            });

        $configMock
            ->expects($this->exactly(2))
            ->method('addTaxonExcludedFromShowingLowestPrice')
            ->with($this->callback(function ($taxon) use ($firstTaxonMock, $secondTaxonMock) {
                return $taxon === $firstTaxonMock || $taxon === $secondTaxonMock;
            }));

        self::assertSame($configMock, $this->channelPriceHistoryConfigDenormalizer->denormalize($data, 'string'));
    }

    public function testRemovesExcludedTaxonsWhenDataHasNone(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var TaxonInterface|MockObject $firstTaxonMock */
        $firstTaxonMock = $this->createMock(TaxonInterface::class);
        /** @var TaxonInterface|MockObject $secondTaxonMock */
        $secondTaxonMock = $this->createMock(TaxonInterface::class);
        /** @var ChannelPriceHistoryConfigInterface|MockObject $configMock */
        $configMock = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        /** @var ChannelPriceHistoryConfigInterface|MockObject $dummyConfigMock */
        $dummyConfigMock = $this->createMock(ChannelPriceHistoryConfigInterface::class);

        $this->channelPriceHistoryConfigDenormalizer->setDenormalizer($denormalizerMock);

        $data = [];

        $this->configFactory->method('createNew')->willReturn($dummyConfigMock);

        $denormalizerMock->expects(self::once())
            ->method('denormalize')
            ->with($data, 'string', null, [self::ALREADY_CALLED => true])
            ->willReturn($configMock);

        $configMock->method('getTaxonsExcludedFromShowingLowestPrice')->willReturn(new ArrayCollection([
            $firstTaxonMock,
            $secondTaxonMock,
        ]));

        $configMock->expects(self::once())->method('clearTaxonsExcludedFromShowingLowestPrice');

        $this->iriConverter->expects(self::never())->method('getResourceFromIri')->with($this->any());

        $configMock->expects(self::never())->method('addTaxonExcludedFromShowingLowestPrice');

        self::assertSame($configMock, $this->channelPriceHistoryConfigDenormalizer->denormalize($data, 'string'));
    }

    public function testReplacesCurrentExcludedTaxonsWithOnesFromData(): void
    {
        /** @var DenormalizerInterface|MockObject $denormalizerMock */
        $denormalizerMock = $this->createMock(DenormalizerInterface::class);
        /** @var TaxonInterface|MockObject $firstCurrentTaxonMock */
        $firstCurrentTaxonMock = $this->createMock(TaxonInterface::class);
        /** @var TaxonInterface|MockObject $secondCurrentTaxonMock */
        $secondCurrentTaxonMock = $this->createMock(TaxonInterface::class);
        /** @var TaxonInterface|MockObject $firstNewTaxonMock */
        $firstNewTaxonMock = $this->createMock(TaxonInterface::class);
        /** @var TaxonInterface|MockObject $secondNewTaxonMock */
        $secondNewTaxonMock = $this->createMock(TaxonInterface::class);
        /** @var ChannelPriceHistoryConfigInterface|MockObject $configMock */
        $configMock = $this->createMock(ChannelPriceHistoryConfigInterface::class);

        $this->channelPriceHistoryConfigDenormalizer->setDenormalizer($denormalizerMock);

        $data = ['taxonsExcludedFromShowingLowestPrice' => [
            '/api/v2/taxons/first-new-taxon',
            '/api/v2/taxons/second-new-taxon',
        ]];

        $denormalizerMock
            ->expects(self::once())
            ->method('denormalize')
            ->with($data, 'string', null, [self::ALREADY_CALLED => true])
            ->willReturn($configMock);

        $configMock
            ->method('getTaxonsExcludedFromShowingLowestPrice')
            ->willReturn(new ArrayCollection([$firstCurrentTaxonMock, $secondCurrentTaxonMock]));

        $configMock
            ->expects(self::once())
            ->method('clearTaxonsExcludedFromShowingLowestPrice');

        $this->iriConverter
            ->expects($this->exactly(2))
            ->method('getResourceFromIri')
            ->with($this->callback(function ($iri) {
                return in_array($iri, [
                    '/api/v2/taxons/first-new-taxon',
                    '/api/v2/taxons/second-new-taxon',
                ], true);
            }))
            ->willReturnCallback(function ($iri) use ($firstNewTaxonMock, $secondNewTaxonMock) {
                return $iri === '/api/v2/taxons/first-new-taxon' ? $firstNewTaxonMock : $secondNewTaxonMock;
            });

        $configMock
            ->expects($this->exactly(2))
            ->method('addTaxonExcludedFromShowingLowestPrice')
            ->with($this->callback(function ($taxon) use ($firstNewTaxonMock, $secondNewTaxonMock) {
                return $taxon === $firstNewTaxonMock || $taxon === $secondNewTaxonMock;
            }));

        self::assertSame($configMock, $this->channelPriceHistoryConfigDenormalizer->denormalize($data, 'string'));
    }
}
