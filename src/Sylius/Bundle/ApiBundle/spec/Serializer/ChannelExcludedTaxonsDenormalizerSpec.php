<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Core\Api\IriConverterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ChannelExcludedTaxonsDenormalizerSpec extends ObjectBehavior
{
    private const ALREADY_CALLED = 'sylius_channel_excluded_taxons_denormalizer_already_called';

    function let(IriConverterInterface $iriConverter): void
    {
        $this->beConstructedWith($iriConverter);
    }

    function it_does_not_support_denormalization_when_the_denormalizer_has_already_been_called(): void
    {
        $this->supportsDenormalization([], 'string', context: [self::ALREADY_CALLED => true])->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_data_is_not_an_array(): void
    {
        $this->supportsDenormalization('string', 'string')->shouldReturn(false);
    }

    function it_does_not_support_denormalization_when_type_is_not_a_channel(): void
    {
        $this->supportsDenormalization([], 'string')->shouldReturn(false);
    }

    function it_throws_an_exception_when_denormalizing_an_object_that_is_not_a_channel(
        DenormalizerInterface $denormalizer,
    ): void {
        $this->setDenormalizer($denormalizer);

        $denormalizer->denormalize([], 'string', null, [self::ALREADY_CALLED => true])->willReturn(new \stdClass());

        $this->shouldThrow(\InvalidArgumentException::class)->during('denormalize', [[], 'string']);
    }

    function it_adds_excluded_taxons_from_data(
        DenormalizerInterface $denormalizer,
        IriConverterInterface $iriConverter,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        ChannelInterface $channel,
    ): void {
        $this->setDenormalizer($denormalizer);

        $data = ['taxonsExcludedFromShowingLowestPrice' => [
            '/api/v2/taxons/first-new-taxon',
            '/api/v2/taxons/second-new-taxon',
        ]];

        $channel->getTaxonsExcludedFromShowingLowestPrice()->willReturn(new ArrayCollection());

        $denormalizer->denormalize($data, 'string', null, [self::ALREADY_CALLED => true])->willReturn($channel);

        $channel->clearTaxonsExcludedFromShowingLowestPrice()->shouldBeCalled();

        $iriConverter->getItemFromIri('/api/v2/taxons/first-new-taxon')->shouldBeCalledTimes(1)->willReturn($firstTaxon);
        $iriConverter->getItemFromIri('/api/v2/taxons/second-new-taxon')->shouldBeCalledTimes(1)->willReturn($secondTaxon);

        $channel->addTaxonExcludedFromShowingLowestPrice($firstTaxon)->shouldBeCalledTimes(1);
        $channel->addTaxonExcludedFromShowingLowestPrice($secondTaxon)->shouldBeCalledTimes(1);

        $this->denormalize($data, 'string')->shouldReturn($channel);
    }

    function it_removes_excluded_taxons_when_data_has_none(
        DenormalizerInterface $denormalizer,
        IriConverterInterface $iriConverter,
        TaxonInterface $firstTaxon,
        TaxonInterface $secondTaxon,
        ChannelInterface $channel,
    ): void {
        $this->setDenormalizer($denormalizer);

        $data = [];

        $denormalizer->denormalize($data, 'string', null, [self::ALREADY_CALLED => true])->willReturn($channel);

        $channel->getTaxonsExcludedFromShowingLowestPrice()->willReturn(new ArrayCollection([
            $firstTaxon->getWrappedObject(),
            $secondTaxon->getWrappedObject(),
        ]));

        $channel->clearTaxonsExcludedFromShowingLowestPrice()->shouldBeCalled();

        $iriConverter->getItemFromIri(Argument::cetera())->shouldNotBeCalled();

        $channel->addTaxonExcludedFromShowingLowestPrice(Argument::any())->shouldNotBeCalled();

        $this->denormalize($data, 'string')->shouldReturn($channel);
    }

    function it_replaces_current_excluded_taxons_with_ones_from_data(
        DenormalizerInterface $denormalizer,
        IriConverterInterface $iriConverter,
        TaxonInterface $firstCurrentTaxon,
        TaxonInterface $secondCurrentTaxon,
        TaxonInterface $firstNewTaxon,
        TaxonInterface $secondNewTaxon,
        ChannelInterface $channel,
    ): void {
        $this->setDenormalizer($denormalizer);

        $data = ['taxonsExcludedFromShowingLowestPrice' => [
            '/api/v2/taxons/first-new-taxon',
            '/api/v2/taxons/second-new-taxon',
        ]];

        $denormalizer->denormalize($data, 'string', null, [self::ALREADY_CALLED => true])->willReturn($channel);

        $channel->getTaxonsExcludedFromShowingLowestPrice()->willReturn(new ArrayCollection([
            $firstCurrentTaxon->getWrappedObject(),
            $secondCurrentTaxon->getWrappedObject(),
        ]));

        $channel->clearTaxonsExcludedFromShowingLowestPrice()->shouldBeCalled();

        $iriConverter->getItemFromIri('/api/v2/taxons/first-new-taxon')->shouldBeCalledTimes(1)->willReturn($firstNewTaxon);
        $iriConverter->getItemFromIri('/api/v2/taxons/second-new-taxon')->shouldBeCalledTimes(1)->willReturn($secondNewTaxon);

        $channel->addTaxonExcludedFromShowingLowestPrice($firstNewTaxon)->shouldBeCalledTimes(1);
        $channel->addTaxonExcludedFromShowingLowestPrice($secondNewTaxon)->shouldBeCalledTimes(1);

        $this->denormalize($data, 'string')->shouldReturn($channel);
    }
}
