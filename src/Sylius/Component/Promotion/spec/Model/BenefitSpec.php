<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\Benefit;
use Sylius\Component\Promotion\Model\BenefitInterface;

class BenefitSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Benefit::class);
    }

    function it_is_Sylius_promotion_benefit()
    {
        $this->shouldImplement(BenefitInterface::class);
    }

    function it_does_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function its_id_is_mutable()
    {
        $this->setId('foo')->shouldBe(null);
        $this->getId()->shouldBe('foo');
    }

    function it_does_not_have_type_by_default()
    {
        $this->getType()->shouldBe(null);
    }

    function its_type_is_mutable()
    {
        $this->setType('foo')->shouldBe(null);
        $this->getType()->shouldBe('foo');
    }

    function it_does_not_have_configuration_by_default()
    {
        $this->getConfiguration()->shouldBe(null);
    }

    function its_configuration_is_mutable()
    {
        $this->setConfiguration(['foo' => 'bar'])->shouldBe(null);
        $this->getConfiguration()->shouldBe(['foo' => 'bar']);
    }
}