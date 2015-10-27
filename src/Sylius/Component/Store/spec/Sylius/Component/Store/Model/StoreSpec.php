<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Store\Model;

use PhpSpec\ObjectBehavior;

class StoreSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Store\Model\Store');
    }

    function it_implements_Sylius_store_interface()
    {
        $this->shouldImplement('Sylius\Component\Store\Model\StoreInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('GERMANY-1');
        $this->getCode()->shouldReturn('GERMANY-1');
    }

    function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_immutable()
    {
        $this->setName('Supershop.de');
        $this->getName()->shouldReturn('Supershop.de');
    }

    function it_has_no_url_by_default()
    {
        $this->getUrl()->shouldReturn(null);
    }

    function its_url_is_mutable()
    {
        $this->setUrl('http://www.supershop.de');
        $this->getUrl()->shouldReturn('http://www.supershop.de');
    }

    function it_is_not_default()
    {
        $this->shouldNotBeDefault();
    }

    function it_can_be_defined_as_default()
    {
        $this->setDefault(true);
        $this->shouldBeDefault();
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable()
    {
        $date = new \DateTime('last year');

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
