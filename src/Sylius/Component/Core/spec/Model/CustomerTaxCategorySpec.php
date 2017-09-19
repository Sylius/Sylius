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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Core\Model\TaxRateInterface;

final class CustomerTaxCategorySpec extends ObjectBehavior
{
    function it_implements_customer_tax_category_interface(): void
    {
        $this->shouldImplement(CustomerTaxCategoryInterface::class);
    }

    function it_does_not_have_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    function it_does_not_have_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('retail');
        $this->getCode()->shouldReturn('retail');
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Retail');
        $this->getName()->shouldReturn('Retail');
    }

    function its_description_is_mutable(): void
    {
        $this->setDescription('Retail customers.');
        $this->getDescription()->shouldReturn('Retail customers.');
    }

    function it_adds_a_rate(TaxRateInterface $rate): void
    {
        $this->addRate($rate);
        $this->hasRate($rate)->shouldReturn(true);
    }

    function it_removes_a_rate(TaxRateInterface $rate): void
    {
        $this->addRate($rate);
        $this->removeRate($rate);
        $this->hasRate($rate)->shouldReturn(false);
    }

    function it_returns_rates(TaxRateInterface $firstRate, TaxRateInterface $secondRate): void
    {
        $this->addRate($firstRate);
        $this->addRate($secondRate);

        $this->getRates()->shouldBeLike(
            new ArrayCollection([$firstRate->getWrappedObject(), $secondRate->getWrappedObject()])
        );
    }
}
