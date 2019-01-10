<?php

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ShopBillingDataInterface;

final class ShopBillingDataSpec extends ObjectBehavior
{
    function it_implements_shop_billing_data_interface(): void
    {
        $this->shouldImplement(ShopBillingDataInterface::class);
    }

    function its_company_is_mutable(): void
    {
        $this->setCompany('Ragnarok');
        $this->getCompany()->shouldReturn('Ragnarok');
    }

    function its_tax_id_is_mutable(): void
    {
        $this->setTaxId('1100110011');
        $this->getTaxId()->shouldReturn('1100110011');
    }

    function its_country_code_is_mutable(): void
    {
        $this->setCountryCode('US');
        $this->getCountryCode()->shouldReturn('US');
    }

    function its_street_is_mutable(): void
    {
        $this->setStreet('Blue Street');
        $this->getStreet()->shouldReturn('Blue Street');
    }

    function its_city_is_mutable(): void
    {
        $this->setCity('New York');
        $this->getCity()->shouldReturn('New York');
    }

    function its_postcode_is_mutable(): void
    {
        $this->setPostcode('94111');
        $this->getPostcode()->shouldReturn('94111');
    }
}
