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
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Serializer\ZoneDenormalizer;
use Sylius\Component\Addressing\Model\Zone;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ZoneDenormalizerSpec extends ObjectBehavior
{
    function let(IriConverterInterface $iriConverter): void
    {
        $this->beConstructedWith($iriConverter);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ZoneDenormalizer::class);
    }

    function it_supports_only_zone_interface(ZoneInterface $zone): void
    {
        $this
            ->supportsDenormalization(
                new Zone(),
                ZoneInterface::class,
                null,
                [AbstractNormalizer::OBJECT_TO_POPULATE => $zone],
            )
            ->shouldReturn(true)
        ;

        $this
            ->supportsDenormalization(
                new Zone(),
                ProductInterface::class,
                null,
                [AbstractNormalizer::OBJECT_TO_POPULATE => $zone],
            )
            ->shouldReturn(false)
        ;
    }

    function it_does_not_support_denormalization_if_object_to_populate_is_not_a_zone(ProductInterface $product): void
    {
        $this
            ->supportsDenormalization(
                new Zone(),
                ZoneInterface::class,
                null,
                [AbstractNormalizer::OBJECT_TO_POPULATE => $product],
            )
            ->shouldReturn(false)
        ;

        $this
            ->supportsDenormalization(
                new Zone(),
                ZoneInterface::class,
            )
            ->shouldReturn(false)
        ;

        $this
            ->supportsDenormalization(
                new Zone(),
                ZoneInterface::class,
                null,
                [AbstractNormalizer::OBJECT_TO_POPULATE => 'I am even not an object!'],
            )
            ->shouldReturn(false)
        ;
    }

    function it_fixes_members_that_were_removed_and_added_again_and_uses_default_denormalizer(
        IriConverterInterface $iriConverter,
        ZoneInterface $zone,
        ZoneMemberInterface $belgiumZone,
        ZoneMemberInterface $germanyZone,
        ZoneMemberInterface $franceZone,
        DenormalizerInterface $denormalizer,
    ): void {
        $belgiumZone->getCode()->willReturn('EU-BE');
        $germanyZone->getCode()->willReturn('EU-DE');
        $franceZone->getCode()->willReturn('EU-FR');

        $zone->getMembers()->willReturn(new ArrayCollection([
            $belgiumZone->getWrappedObject(),
            $germanyZone->getWrappedObject(),
            $franceZone->getWrappedObject(),
        ]));

        $iriConverter->getIriFromResource($belgiumZone)->willReturn('iri/EU-BE');
        $iriConverter->getIriFromResource($germanyZone)->willReturn('iri/EU-DE');
        $iriConverter->getIriFromResource($franceZone)->willReturn('iri/EU-FR');

        $denormalizer->denormalize(
            [
                'members' => [
                    'iri/EU-BE',
                    ['code' => 'EU-PL'],
                    'iri/EU-FR',
                ],
            ],
            Zone::class,
            null,
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $zone,
                'sylius_zone_denormalizer_already_called' => true,
            ],
        )->shouldBeCalled();

        $this->setDenormalizer($denormalizer);
        $this->denormalize(
            [
                'members' => [
                    'iri/EU-BE',
                    ['code' => 'EU-FR'],
                    ['code' => 'EU-PL'],
                ],
            ],
            Zone::class,
            null,
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $zone,
            ],
        );
    }

    public function it_does_not_fix_members_if_there_are_no_members_in_data(
        IriConverterInterface $iriConverter,
        ZoneInterface $zone,
        ZoneMemberInterface $belgiumZone,
        ZoneMemberInterface $germanyZone,
        ZoneMemberInterface $franceZone,
        DenormalizerInterface $denormalizer,
    ): void {
        $belgiumZone->getCode()->willReturn('EU-BE');
        $germanyZone->getCode()->willReturn('EU-DE');
        $franceZone->getCode()->willReturn('EU-FR');

        $zone->getMembers()->willReturn(new ArrayCollection([
            $belgiumZone->getWrappedObject(),
            $germanyZone->getWrappedObject(),
            $franceZone->getWrappedObject(),
        ]));

        $iriConverter->getIriFromResource($belgiumZone)->shouldNotBeCalled();
        $iriConverter->getIriFromResource($germanyZone)->shouldNotBeCalled();
        $iriConverter->getIriFromResource($franceZone)->shouldNotBeCalled();

        $denormalizer->denormalize(
            [],
            Zone::class,
            null,
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $zone,
                'sylius_zone_denormalizer_already_called' => true,
            ],
        )->shouldBeCalled();

        $this->setDenormalizer($denormalizer);
        $this->denormalize(
            [],
            Zone::class,
            null,
            [
                AbstractNormalizer::OBJECT_TO_POPULATE => $zone,
            ],
        );
    }
}
