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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerGroupInterface;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Customer\Model\CustomerGroup as BaseCustomerGroup;

final class CustomerGroupSpec extends ObjectBehavior
{
    function it_implements_customer_group_interface(): void
    {
        $this->shouldImplement(CustomerGroupInterface::class);
    }

    function it_extends_a_customer_group_model(): void
    {
        $this->shouldHaveType(BaseCustomerGroup::class);
    }

    function it_does_not_have_a_tax_category_by_default(): void
    {
        $this->getTaxCategory()->shouldReturn(null);
    }

    function it_allows_setting_the_tax_category(CustomerTaxCategoryInterface $taxCategory): void
    {
        $this->setTaxCategory($taxCategory);
        $this->getTaxCategory()->shouldReturn($taxCategory);
    }

    function it_allows_resetting_the_tax_category(CustomerTaxCategoryInterface $taxCategory): void
    {
        $this->setTaxCategory($taxCategory);
        $this->getTaxCategory()->shouldReturn($taxCategory);

        $this->setTaxCategory(null);
        $this->getTaxCategory()->shouldReturn(null);
    }
}
