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

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Api\IriConverterInterface;
use ApiPlatform\Symfony\Validator\Exception\ValidationException;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Validator\ResourceInputDataPropertiesValidatorInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ChannelPriceHistoryConfigDenormalizerSpec extends ObjectBehavior
{
    private const ALREADY_CALLED = 'sylius_channel_price_history_config_denormalizer_already_called';

    function let(
        IriConverterInterface $iriConverter,
        FactoryInterface $configFactory,
        ResourceInputDataPropertiesValidatorInterface $validator,
    ): void {
        $this->beConstructedWith($iriConverter, $configFactory, $validator, []);
    }

    function it_does_not_support_denormalization_when_the_denormalizer_has_already_been_called(): void
    {
        $this->supportsDenormalization([], 'string', context: [self::ALREADY_CALLED => true])->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_data_is_not_an_array(): void
    {
        $this->supportsDenormalization('string', 'string')->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_type_is_not_a_channel_price_history_config(): void
    {
        $this->supportsDenormalization([], 'string')->shouldReturn(false);
    }

    function it_throws_an_exception_when_denormalizing_an_object_that_is_not_a_channel_price_history_config(
        DenormalizerInterface $denormalizer,
        FactoryInterface $configFactory,
        ResourceInputDataPropertiesValidatorInterface $validator,
        ChannelPriceHistoryConfigInterface $dummyConfig,
    ): void {
        $this->setDenormalizer($denormalizer);

        $configFactory->createNew()->willReturn($dummyConfig);
        $validator->validate($dummyConfig, [], [])->shouldBeCalled();

        $denormalizer->denormalize([], 'string', null, [self::ALREADY_CALLED => true])->willReturn(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->during('denormalize', [[], 'string']);
    }

    function it_validates_input_data_before(
        DenormalizerInterface $denormalizer,
        FactoryInterface $configFactory,
        ResourceInputDataPropertiesValidatorInterface $validator,
        ChannelPriceHistoryConfigInterface $dummyConfig,
    ): void {
        $this->setDenormalizer($denormalizer);

        $configFactory->createNew()->willReturn($dummyConfig);
        $validator->validate($dummyConfig, [], [])->willThrow(ValidationException::class);

        $denormalizer->denormalize(Argument::cetera())->shouldNotBeCalled();

        $this->shouldThrow(ValidationException::class)->during('denormalize', [[], 'string']);
    }

    function it_adds_excluded_taxons_from_data(
        DenormalizerInterface $denormalizer,
        IriConverterInterface $iriConverter,
        FactoryInterface $configFactory,
        ResourceInputDataPropertiesValidatorInterface $validator,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        ChannelPriceHistoryConfigInterface $config,
        ChannelPriceHistoryConfigInterface $dummyConfig,
    ): void {
        $this->setDenormalizer($denormalizer);

        $data = ['taxonsExcludedFromShowingLowestPrice' => [
            '/api/v2/taxons/first-new-taxon',
            '/api/v2/taxons/second-new-taxon',
        ]];

        $configFactory->createNew()->willReturn($dummyConfig);
        $validator->validate($dummyConfig, $data, [])->shouldBeCalled();

        $denormalizer->denormalize($data, 'string', null, [self::ALREADY_CALLED => true])->willReturn($config);

        $config->clearTaxonsExcludedFromShowingLowestPrice()->shouldBeCalled();

        $iriConverter->getResourceFromIri('/api/v2/taxons/first-new-taxon')->shouldBeCalledTimes(1)->willReturn($firstTaxon);
        $iriConverter->getResourceFromIri('/api/v2/taxons/second-new-taxon')->shouldBeCalledTimes(1)->willReturn($secondTaxon);

        $config->addTaxonExcludedFromShowingLowestPrice($firstTaxon)->shouldBeCalledTimes(1);
        $config->addTaxonExcludedFromShowingLowestPrice($secondTaxon)->shouldBeCalledTimes(1);

        $this->denormalize($data, 'string')->shouldReturn($config);
    }

    function it_removes_excluded_taxons_when_data_has_none(
        DenormalizerInterface $denormalizer,
        IriConverterInterface $iriConverter,
        FactoryInterface $configFactory,
        ResourceInputDataPropertiesValidatorInterface $validator,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        ChannelPriceHistoryConfigInterface $config,
        ChannelPriceHistoryConfigInterface $dummyConfig,
    ): void {
        $this->setDenormalizer($denormalizer);

        $data = [];

        $configFactory->createNew()->willReturn($dummyConfig);
        $validator->validate($dummyConfig, $data, [])->shouldBeCalled();

        $denormalizer
            ->denormalize($data, 'string', null, [self::ALREADY_CALLED => true])
            ->willReturn($config)
        ;

        $config->getTaxonsExcludedFromShowingLowestPrice()->willReturn(new ArrayCollection([
            $firstTaxon->getWrappedObject(),
            $secondTaxon->getWrappedObject(),
        ]));

        $config->clearTaxonsExcludedFromShowingLowestPrice()->shouldBeCalled();

        $iriConverter->getResourceFromIri(Argument::cetera())->shouldNotBeCalled();

        $config->addTaxonExcludedFromShowingLowestPrice(Argument::any())->shouldNotBeCalled();

        $this->denormalize($data, 'string')->shouldReturn($config);
    }

    function it_replaces_current_excluded_taxons_with_ones_from_data(
        DenormalizerInterface $denormalizer,
        IriConverterInterface $iriConverter,
        FactoryInterface $configFactory,
        ResourceInputDataPropertiesValidatorInterface $validator,
        TaxonInterface $firstCurrentTaxon,
        TaxonInterface $secondCurrentTaxon,
        TaxonInterface $firstNewTaxon,
        TaxonInterface $secondNewTaxon,
        ChannelPriceHistoryConfigInterface $config,
        ChannelPriceHistoryConfigInterface $dummyConfig,
    ): void {
        $this->setDenormalizer($denormalizer);

        $data = ['taxonsExcludedFromShowingLowestPrice' => [
            '/api/v2/taxons/first-new-taxon',
            '/api/v2/taxons/second-new-taxon',
        ]];

        $configFactory->createNew()->willReturn($dummyConfig);
        $validator->validate($dummyConfig, $data, [])->shouldBeCalled();

        $denormalizer
            ->denormalize($data, 'string', null, [self::ALREADY_CALLED => true])
            ->willReturn($config)
        ;

        $config->getTaxonsExcludedFromShowingLowestPrice()->willReturn(new ArrayCollection([
            $firstCurrentTaxon->getWrappedObject(),
            $secondCurrentTaxon->getWrappedObject(),
        ]));

        $config->clearTaxonsExcludedFromShowingLowestPrice()->shouldBeCalled();

        $iriConverter->getResourceFromIri('/api/v2/taxons/first-new-taxon')->shouldBeCalledTimes(1)->willReturn($firstNewTaxon);
        $iriConverter->getResourceFromIri('/api/v2/taxons/second-new-taxon')->shouldBeCalledTimes(1)->willReturn($secondNewTaxon);

        $config->addTaxonExcludedFromShowingLowestPrice($firstNewTaxon)->shouldBeCalledTimes(1);
        $config->addTaxonExcludedFromShowingLowestPrice($secondNewTaxon)->shouldBeCalledTimes(1);

        $this->denormalize($data, 'string')->shouldReturn($config);
    }
}
