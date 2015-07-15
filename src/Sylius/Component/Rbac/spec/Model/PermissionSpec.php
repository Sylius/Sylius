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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PermissionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Model\Permission');
    }

    public function it_implements_Sylius_Rbac_permission_interface()
    {
        $this->shouldImplement('Sylius\Component\Rbac\Model\PermissionInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    public function its_code_is_mutable()
    {
        $this->setCode('can_edit_product');
        $this->getCode()->shouldReturn('can_edit_product');
    }

    public function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    public function it_can_have_a_description()
    {
        $this->setDescription('Can edit product');
        $this->getDescription()->shouldReturn('Can edit product');
    }

    public function its_creation_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    public function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    public function its_last_update_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
