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

namespace spec\Sylius\Component\Shipping\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;

final class ShippingCategorySpec extends ObjectBehavior
{
    public function it_implements_shipping_category_interface(): void
    {
        $this->shouldImplement(ShippingCategoryInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    public function its_code_is_mutable(): void
    {
        $this->setCode('SC2');
        $this->getCode()->shouldReturn('SC2');
    }

    public function it_is_unnamed_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable(): void
    {
        $this->setName('Shippingable goods');
        $this->getName()->shouldReturn('Shippingable goods');
    }

    public function it_has_no_description_by_default(): void
    {
        $this->getDescription()->shouldReturn(null);
    }

    public function its_description_is_mutable(): void
    {
        $this->setDescription('All shippingable goods');
        $this->getDescription()->shouldReturn('All shippingable goods');
    }

    public function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    public function its_creation_date_is_mutable(): void
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    public function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    public function its_last_update_date_is_mutable(): void
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
