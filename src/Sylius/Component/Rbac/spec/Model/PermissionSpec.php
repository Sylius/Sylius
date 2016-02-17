<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Rbac\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Model\PermissionInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PermissionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Model\Permission');
    }

    function it_implements_Sylius_Rbac_permission_interface()
    {
        $this->shouldImplement(PermissionInterface::class);
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
        $this->setCode('can_edit_product');
        $this->getCode()->shouldReturn('can_edit_product');
    }

    function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function it_can_have_a_description()
    {
        $this->setDescription('Can edit product');
        $this->getDescription()->shouldReturn('Can edit product');
    }

    function its_creation_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
