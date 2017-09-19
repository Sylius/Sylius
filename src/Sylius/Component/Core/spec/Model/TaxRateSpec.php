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
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Taxation\Model\TaxRate as BaseTaxRate;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class TaxRateSpec extends ObjectBehavior
{
    function it_implements_a_tax_rate_interface(): void
    {
        $this->shouldImplement(TaxRateInterface::class);
    }

    function it_extends_a_base_tax_rate_model(): void
    {
        $this->shouldHaveType(BaseTaxRate::class);
    }

    function it_does_not_have_any_zone_defined_by_default(): void
    {
        $this->getZone()->shouldReturn(null);
    }

    function it_allows_defining_zone(ZoneInterface $zone): void
    {
        $this->setZone($zone);
        $this->getZone()->shouldReturn($zone);
    }

    function it_does_not_have_a_customer_tax_category_by_default(): void
    {
        $this->getCustomerTaxCategory()->shouldReturn(null);
    }

    function it_allows_setting_the_customer_tax_category(CustomerTaxCategoryInterface $customerTaxCategory): void
    {
        $this->setCustomerTaxCategory($customerTaxCategory);
        $this->getCustomerTaxCategory()->shouldReturn($customerTaxCategory);
    }

    function it_allows_resetting_the_customer_tax_category(CustomerTaxCategoryInterface $customerTaxCategory): void
    {
        $this->setCustomerTaxCategory($customerTaxCategory);
        $this->getCustomerTaxCategory()->shouldReturn($customerTaxCategory);

        $this->setCustomerTaxCategory(null);
        $this->getCustomerTaxCategory()->shouldReturn(null);
    }
}
