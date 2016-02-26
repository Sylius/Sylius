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
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TaxContextSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $taxRateFactory,
        FactoryInterface $taxCategoryFactory,
        RepositoryInterface $taxRateRepository,
        RepositoryInterface $taxCategoryRepository,
        RepositoryInterface $zoneRepository
    ) {
        $this->beConstructedWith(
            $taxRateFactory,
            $taxCategoryFactory,
            $taxRateRepository,
            $taxCategoryRepository,
            $zoneRepository
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\TaxContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_tax_rate_for_given_tax_category_and_zone(
        $taxRateFactory,
        $taxCategoryFactory,
        $taxRateRepository,
        $taxCategoryRepository,
        TaxCategoryInterface $taxCategory,
        TaxRateInterface $taxRate,
        ZoneInterface $zone
    ) {
        $taxCategoryRepository->findOneBy(['name' => 'Clothes'])->willReturn(null);

        $taxCategoryFactory->createNew()->willReturn($taxCategory);
        $taxCategory->setName('Clothes')->shouldBeCalled();
        $taxCategory->setCode('clothes')->shouldBeCalled();

        $taxCategoryRepository->add($taxCategory)->shouldBeCalled();

        $zone->getCode()->willReturn('EU');

        $taxRateFactory->createNew()->willReturn($taxRate);
        $taxRate->setName('Low VAT')->shouldBeCalled();
        $taxRate->setCode('low_vat_eu')->shouldBeCalled();
        $taxRate->setAmount(0.23)->shouldBeCalled();
        $taxRate->setCategory($taxCategory)->shouldBeCalled();
        $taxRate->setZone($zone)->shouldBeCalled();
        $taxRate->setCalculator('default')->shouldBeCalled();

        $taxRateRepository->add($taxRate)->shouldBeCalled();

        $this->storeHasTaxRateWithinZone('Low VAT', '23%', 'Clothes', $zone);
    }
}
