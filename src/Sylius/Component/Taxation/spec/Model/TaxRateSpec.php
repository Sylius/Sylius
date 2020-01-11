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

namespace spec\Sylius\Component\Taxation\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

final class TaxRateSpec extends ObjectBehavior
{
    function it_implements_tax_rate_interface(): void
    {
        $this->shouldImplement(TaxRateInterface::class);
    }

    function it_does_not_have_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_belong_to_category_by_default(): void
    {
        $this->getCategory()->shouldReturn(null);
    }

    function it_allows_assigning_itself_to_category(TaxCategoryInterface $category): void
    {
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);
    }

    function it_allows_detaching_itself_from_category(TaxCategoryInterface $category): void
    {
        $this->setCategory($category);

        $this->setCategory(null);
        $this->getCategory()->shouldReturn(null);
    }

    function it_is_unnamed_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable(): void
    {
        $this->setName('Taxable goods');
        $this->getName()->shouldReturn('Taxable goods');
    }

    function it_has_mutable_code(): void
    {
        $this->setCode('TR1');
        $this->getCode()->shouldReturn('TR1');
    }

    function it_has_amount_equal_to_0_by_default(): void
    {
        $this->getAmount()->shouldReturn(0.00);
    }

    function its_amount_should_be_mutable(): void
    {
        $this->setAmount(0.23);
        $this->getAmount()->shouldReturn(0.23);
    }

    function it_represents_amount_as_percentage(): void
    {
        $this->setAmount(0.23);
        $this->getAmountAsPercentage()->shouldReturn(23.00);

        $this->setAmount(0.125);
        $this->getAmountAsPercentage()->shouldReturn(12.5);
    }

    function it_is_not_included_in_price_by_default(): void
    {
        $this->shouldNotBeIncludedInPrice();
    }

    function its_inclusion_in_price_should_be_mutable(): void
    {
        $this->setIncludedInPrice(true);
        $this->shouldBeIncludedInPrice();
    }

    function it_dose_not_have_calculator_defined_by_default(): void
    {
        $this->getCalculator()->shouldReturn(null);
    }

    function its_calculator_should_be_mutable(): void
    {
        $this->setCalculator('default');
        $this->getCalculator()->shouldReturn('default');
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    function it_does_not_have_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function it_has_label(): void
    {
        $this->setName('Test tax');
        $this->setAmount(0.23);

        $this->getLabel()->shouldReturn('Test tax (23%)');
    }
}
