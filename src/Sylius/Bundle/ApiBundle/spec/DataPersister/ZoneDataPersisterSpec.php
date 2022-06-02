<?php

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\ZoneCannotBeRemoved;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ZoneDataPersisterSpec extends ObjectBehavior
{
    function let(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        RepositoryInterface $zoneMemberRepository,
    ): void {
        $this->beConstructedWith($decoratedDataPersister, $zoneMemberRepository);
    }

    function it_supports_only_zone_entity(ZoneInterface $zone, ProductInterface $product): void
    {
        $this->supports($zone)->shouldReturn(true);
        $this->supports($product)->shouldReturn(false);
    }

    function it_uses_decorated_data_persister_to_persist_zone(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        ZoneInterface $zone,
    ): void {
        $decoratedDataPersister->persist($zone, [])->shouldBeCalled();

        $this->persist($zone, []);
    }

    function it_uses_decorated_data_persister_to_remove_zone(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        RepositoryInterface $zoneMemberRepository,
        ZoneInterface $zone,
    ): void {
        $zone->getCode()->willReturn('US');
        $zoneMemberRepository->findOneBy(['code' => 'US'])->willReturn(null);

        $decoratedDataPersister->remove($zone, [])->shouldBeCalled();

        $this->remove($zone, []);
    }

    function it_throws_an_error_if_the_zone_is_in_use(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        RepositoryInterface $zoneMemberRepository,
        ZoneInterface $zone,
        ZoneMemberInterface $zoneMember,
    ): void {
        $zone->getCode()->willReturn('US');
        $zoneMemberRepository->findOneBy(['code' => 'US'])->willReturn($zoneMember);

        $decoratedDataPersister->remove($zone, [])->shouldNotBeCalled();

        $this
            ->shouldThrow(ZoneCannotBeRemoved::class)
            ->during('remove', [$zone, []])
        ;
    }
}
