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

final class TaxCategorySpec extends ObjectBehavior
{
    function it_implements_tax_category_interface(): void
    {
        $this->shouldImplement(TaxCategoryInterface::class);
    }

    function it_does_not_have_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_mutable_code(): void
    {
        $this->setCode('TC1');
        $this->getCode()->shouldReturn('TC1');
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

    function it_does_not_have_description_by_default(): void
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_should_be_mutable(): void
    {
        $this->setDescription('All taxable goods');
        $this->getDescription()->shouldReturn('All taxable goods');
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    function it_does_not_have_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
