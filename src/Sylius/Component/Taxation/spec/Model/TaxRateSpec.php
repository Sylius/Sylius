<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Taxation\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class TaxRateSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxation\Model\TaxRate');
    }

    function it_should_implement_Sylius_tax_rate_interface()
    {
        $this->shouldImplement(TaxRateInterface::class);
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_belong_to_category_by_default()
    {
        $this->getCategory()->shouldReturn(null);
    }

    function it_should_allow_assigning_itself_to_category(TaxCategoryInterface $category)
    {
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);
    }

    function it_should_allow_detaching_itself_from_category(TaxCategoryInterface $category)
    {
        $this->setCategory($category);

        $this->setCategory(null);
        $this->getCategory()->shouldReturn(null);
    }

    function it_should_be_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable()
    {
        $this->setName('Taxable goods');
        $this->getName()->shouldReturn('Taxable goods');
    }

    function it_has_mutable_code()
    {
        $this->setCode('TR1');
        $this->getCode()->shouldReturn('TR1');
    }

    function it_should_have_amount_equal_to_0_by_default()
    {
        $this->getAmount()->shouldReturn(0);
    }

    function its_amount_should_be_mutable()
    {
        $this->setAmount(0.23);
        $this->getAmount()->shouldReturn(0.23);
    }

    function it_should_represent_amount_as_percentage()
    {
        $this->setAmount(0.23);
        $this->getAmountAsPercentage()->shouldReturn(23.00);

        $this->setAmount(0.125);
        $this->getAmountAsPercentage()->shouldReturn(12.5);
    }

    function it_should_not_be_included_in_price_by_default()
    {
        $this->shouldNotBeIncludedInPrice();
    }

    function its_inclusion_in_price_should_be_mutable()
    {
        $this->setIncludedInPrice(true);
        $this->shouldBeIncludedInPrice();
    }

    function it_should_not_have_calculator_defined_by_default()
    {
        $this->getCalculator()->shouldReturn(null);
    }

    function its_calculator_should_be_mutable()
    {
        $this->setCalculator('default');
        $this->getCalculator()->shouldReturn('default');
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function it_has_label()
    {
        $this->setName('Test tax');
        $this->setAmount(0.23);

        $this->getLabel()->shouldReturn('Test tax (23%)');
    }
}
