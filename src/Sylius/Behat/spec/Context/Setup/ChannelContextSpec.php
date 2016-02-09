<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\DefaultStoreDataInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ChannelContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        DefaultStoreDataInterface $defaultFranceChannelFactory,
        FactoryInterface $countryFactory,
        RepositoryInterface $countryRepository
    ) {
        $this->beConstructedWith(
            $sharedStorage,
            $defaultFranceChannelFactory,
            $countryFactory,
            $countryRepository
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\ChannelContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_sets_default_channel_in_the_shared_storage(
        $defaultFranceChannelFactory,
        $sharedStorage,
        ChannelInterface $channel,
        ZoneInterface $zone
    ) {
        $defaultData = ['channel' => $channel, 'zone' => $zone];
        $defaultFranceChannelFactory->create()->willReturn($defaultData);
        $sharedStorage->setClipboard($defaultData)->shouldBeCalled();

        $this->thatStoreIsOperatingOnASingleChannel();
    }

    function it_configures_shipping_destination_countries(
        $countryFactory,
        $countryRepository,
        CountryInterface $australia,
        CountryInterface $china,
        CountryInterface $france
    ) {
        $countryFactory->createNew()->willReturn($australia, $china, $france);

        $australia->setCode('AU')->shouldBeCalled();
        $china->setCode('CN')->shouldBeCalled();
        $france->setCode('FR')->shouldBeCalled();

        $countryRepository->add($australia)->shouldBeCalled();
        $countryRepository->add($china)->shouldBeCalled();
        $countryRepository->add($france)->shouldBeCalled();

        $this->storeShipsTo('Australia', 'China', 'France');
    }
}
