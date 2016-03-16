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
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ShippingMethod;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ShippingContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $shippingMethodRepository,
        RepositoryInterface $zoneRepository,
        FactoryInterface $shippingMethodFactory,
        ObjectManager $shippingMethodManager
    ) {
        $this->beConstructedWith(
            $sharedStorage,
            $shippingMethodRepository,
            $zoneRepository,
            $shippingMethodFactory,
            $shippingMethodManager
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\ShippingContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_configures_store_to_ship_everything_for_free(
        $shippingMethodRepository,
        $shippingMethodFactory,
        $sharedStorage,
        ShippingMethod $shippingMethod,
        ZoneInterface $zone
    ) {
        $sharedStorage->get('zone')->willReturn($zone);
        $shippingMethodFactory->createNew()->willReturn($shippingMethod);

        $zone->getCode()->willReturn('UE');

        $shippingMethod->setCode('free_ue')->shouldBeCalled();
        $shippingMethod->setName('Free')->shouldBeCalled();
        $shippingMethod->setCurrentLocale('en')->shouldBeCalled();
        $shippingMethod->setConfiguration(['amount' => 0])->shouldBeCalled();
        $shippingMethod->setCalculator(DefaultCalculators::FLAT_RATE)->shouldBeCalled();
        $shippingMethod->setZone($zone)->shouldBeCalled();

        $shippingMethodRepository->add($shippingMethod)->shouldBeCalled();

        $this->storeShipsEverythingForFree();
    }

    function it_configures_shipping_method_with_given_data(
        $shippingMethodRepository,
        $shippingMethodFactory,
        $sharedStorage,
        ShippingMethod $shippingMethod,
        ZoneInterface $zone
    ) {
        $sharedStorage->get('zone')->willReturn($zone);
        $shippingMethodFactory->createNew()->willReturn($shippingMethod);

        $zone->getCode()->willReturn('UE');

        $shippingMethod->setCode('test_shipping_method_ue')->shouldBeCalled();
        $shippingMethod->setName('Test shipping method')->shouldBeCalled();
        $shippingMethod->setCurrentLocale('en')->shouldBeCalled();
        $shippingMethod->setConfiguration(['amount' => 1000])->shouldBeCalled();
        $shippingMethod->setCalculator(DefaultCalculators::FLAT_RATE)->shouldBeCalled();
        $shippingMethod->setZone($zone)->shouldBeCalled();

        $shippingMethodRepository->add($shippingMethod)->shouldBeCalled();

        $this->storeHasShippingMethodWithFee('Test shipping method', 1000);
    }

    function it_configures_store_to_ship_everything_for_free_in_every_available_zone(
        $shippingMethodRepository,
        $zoneRepository,
        $shippingMethodFactory,
        ShippingMethod $euShippingMethod,
        ShippingMethod $usShippingMethod,
        ZoneInterface $euZone,
        ZoneInterface $usZone
    ) {
        $zoneRepository->findAll()->willReturn([$euZone, $usZone]);

        $shippingMethodFactory->createNew()->willReturn($euShippingMethod, $usShippingMethod);

        $euZone->getCode()->willReturn('UE');

        $euShippingMethod->setCode('free_ue')->shouldBeCalled();
        $euShippingMethod->setName('Free')->shouldBeCalled();
        $euShippingMethod->setCurrentLocale('en')->shouldBeCalled();
        $euShippingMethod->setConfiguration(['amount' => 0])->shouldBeCalled();
        $euShippingMethod->setCalculator(DefaultCalculators::FLAT_RATE)->shouldBeCalled();
        $euShippingMethod->setZone($euZone)->shouldBeCalled();

        $usZone->getCode()->willReturn('US');

        $usShippingMethod->setCode('free_us')->shouldBeCalled();
        $usShippingMethod->setName('Free')->shouldBeCalled();
        $usShippingMethod->setCurrentLocale('en')->shouldBeCalled();
        $usShippingMethod->setConfiguration(['amount' => 0])->shouldBeCalled();
        $usShippingMethod->setCalculator(DefaultCalculators::FLAT_RATE)->shouldBeCalled();
        $usShippingMethod->setZone($usZone)->shouldBeCalled();

        $shippingMethodRepository->add($euShippingMethod)->shouldBeCalled();
        $shippingMethodRepository->add($usShippingMethod)->shouldBeCalled();

        $this->theStoreShipsEverywhereForFree();
    }

    function it_assigns_product_for_given_tax_category(
        $shippingMethodManager,
        ShippingMethodInterface $shippingMethod,
        TaxCategoryInterface $taxCategory
    ) {
        $shippingMethod->setTaxCategory($taxCategory)->shouldBeCalled();
        $shippingMethodManager->flush()->shouldBeCalled();

        $this->shippingMethodBelongsToTaxCategory($shippingMethod, $taxCategory);
    }
}
