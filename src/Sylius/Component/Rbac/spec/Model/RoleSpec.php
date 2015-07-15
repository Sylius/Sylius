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
class RoleSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Model\Role');
    }

    public function it_implements_Sylius_Rbac_role_interface()
    {
        $this->shouldImplement('Sylius\Component\Rbac\Model\RoleInterface');
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
        $this->setCode('catalog_manager');
        $this->getCode()->shouldReturn('catalog_manager');
    }

    public function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function it_can_have_a_name()
    {
        $this->setName('Catalog Manager');
        $this->getName()->shouldReturn('Catalog Manager');
    }

    public function it_does_not_have_any_permissions_by_default()
    {
        $this->getPermissions()->shouldHaveType('Doctrine\Common\Collections\ArrayCollection');
    }

    public function it_can_have_specific_permissions(PermissionInterface $permission)
    {
        $this->hasPermission($permission)->shouldReturn(false);
        $this->addPermission($permission);
        $this->hasPermission($permission)->shouldReturn(true);
    }

    public function it_can_remove_permissions(PermissionInterface $permission)
    {
        $this->addPermission($permission);
        $this->removePermission($permission);
        $this->hasPermission($permission)->shouldReturn(false);
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
