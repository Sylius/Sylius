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

namespace spec\Sylius\Bundle\ApiBundle\Serializer\Denormalizer;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ZoneDenormalizerSpec extends ObjectBehavior
{
    public function let(
        DenormalizerInterface $denormalizer,
        SectionProviderInterface $sectionProvider,
    ): void {
        $this->beConstructedWith($denormalizer, $sectionProvider);
    }

    public function it_supports_only_admin_section(
        SectionProviderInterface $sectionProvider,
        ZoneInterface $zone
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());
        $this
            ->supportsDenormalization([], ZoneInterface::class, null, [AbstractNormalizer::OBJECT_TO_POPULATE => $zone])
            ->shouldReturn(true)
        ;

        $sectionProvider->getSection()->willReturn(new ShopApiSection());
        $this
            ->supportsDenormalization([], ZoneInterface::class, null, [AbstractNormalizer::OBJECT_TO_POPULATE => $zone])
            ->shouldReturn(false)
        ;
    }

    public function it_supports_only_objects_to_populate_is_zone_interface(
        SectionProviderInterface $sectionProvider,
        ZoneInterface $zone,
        ProductInterface $product,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this
            ->supportsDenormalization([], ZoneInterface::class, null, [AbstractNormalizer::OBJECT_TO_POPULATE => $zone])
            ->shouldReturn(true)
        ;

        $this
            ->supportsDenormalization([], ZoneInterface::class)
            ->shouldReturn(false)
        ;

        $this
            ->supportsDenormalization([], ZoneInterface::class, null, [AbstractNormalizer::OBJECT_TO_POPULATE => null])
            ->shouldReturn(false)
        ;

        $this
            ->supportsDenormalization([], ZoneInterface::class, null, [AbstractNormalizer::OBJECT_TO_POPULATE => []])
            ->shouldReturn(false)
        ;

        $this
            ->supportsDenormalization([], ZoneInterface::class, null, [AbstractNormalizer::OBJECT_TO_POPULATE => $product])
            ->shouldReturn(false)
        ;
    }

    public function it_supports_only_zone_interface(
        SectionProviderInterface $sectionProvider,
        ZoneInterface $zone
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this
            ->supportsDenormalization([], ZoneInterface::class, null, [AbstractNormalizer::OBJECT_TO_POPULATE => $zone])
            ->shouldReturn(true)
        ;

        $this
            ->supportsDenormalization([], ProductInterface::class, null, [AbstractNormalizer::OBJECT_TO_POPULATE => $zone])
            ->shouldReturn(false)
        ;
    }

    public function it_does_not_replace_members_if_curent_zone_members_are_not_present(
        DenormalizerInterface $denormalizer,
        SectionProviderInterface $sectionProvider,
        ZoneInterface $zone,
        ZoneInterface $objectToPopulate,
        ZoneMemberInterface $memberUS,
        ZoneMemberInterface $memberUK,
        ZoneMemberInterface $memberPL,
        ZoneMemberInterface $memberDE,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());
        $memberUS->getCode()->willReturn('US');
        $memberUK->getCode()->willReturn('UK');
        $memberPL->getCode()->willReturn('PL');
        $memberDE->getCode()->willReturn('DE');

        $objectToPopulate->getMembers()->willReturn(new ArrayCollection([
            $memberUS->getWrappedObject(),
            $memberUK->getWrappedObject()
        ]));
        $zone->getMembers()->willReturn(new ArrayCollection([
            $memberPL->getWrappedObject(),
            $memberDE->getWrappedObject()
        ]));

        $context = [
            AbstractNormalizer::OBJECT_TO_POPULATE => $objectToPopulate,
            'sylius_zone_denormalizer_already_called' => true,
        ];
        $denormalizer->denormalize([], ZoneInterface::class, null, $context)->willReturn($zone);

        $zone->removeMember(Argument::any())->shouldNotBeCalled();
        $zone->addMember(Argument::any())->shouldNotBeCalled();

        $this->denormalize([], ZoneInterface::class, null, [AbstractNormalizer::OBJECT_TO_POPULATE => $objectToPopulate]);
    }

    public function it_replace_members_if_curent_zone_members_are_present(
        DenormalizerInterface $denormalizer,
        SectionProviderInterface $sectionProvider,
        ZoneInterface $zone,
        ZoneInterface $objectToPopulate,
        ZoneMemberInterface $memberUS,
        ZoneMemberInterface $memberUK,
        ZoneMemberInterface $memberPL,
        ZoneMemberInterface $newMemberUS,
    ): void {
        $sectionProvider->getSection()->willReturn(new AdminApiSection());
        $memberUS->getCode()->willReturn('US');
        $memberUK->getCode()->willReturn('UK');
        $memberPL->getCode()->willReturn('PL');
        $newMemberUS->getCode()->willReturn('US');

        $objectToPopulate->getMembers()->willReturn(new ArrayCollection([
            $memberUS->getWrappedObject(),
            $memberUK->getWrappedObject()
        ]));
        $zone->getMembers()->willReturn(new ArrayCollection([
            $memberPL->getWrappedObject(),
            $newMemberUS->getWrappedObject()
        ]));

        $context = [
            AbstractNormalizer::OBJECT_TO_POPULATE => $objectToPopulate,
            'sylius_zone_denormalizer_already_called' => true,
        ];
        $denormalizer->denormalize([], ZoneInterface::class, null, $context)->willReturn($zone);

        $zone->removeMember($newMemberUS)->shouldBeCalled();
        $zone->addMember($memberUS)->shouldBeCalled();

        $this->denormalize([], ZoneInterface::class, null, [AbstractNormalizer::OBJECT_TO_POPULATE => $objectToPopulate]);
    }
}
