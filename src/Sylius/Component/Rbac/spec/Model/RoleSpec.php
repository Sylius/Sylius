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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RoleSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Rbac\Model\Role');
    }

    function it_implements_Sylius_Rbac_role_interface()
    {
        $this->shouldImplement(RoleInterface::class);
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
        $this->setCode('catalog_manager');
        $this->getCode()->shouldReturn('catalog_manager');
    }

    function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function it_can_have_a_name()
    {
        $this->setName('Catalog Manager');
        $this->getName()->shouldReturn('Catalog Manager');
    }

    function it_does_not_have_any_permissions_by_default()
    {
        $this->getPermissions()->shouldHaveType(ArrayCollection::class);
    }

    function it_can_have_specific_permissions(PermissionInterface $permission)
    {
        $this->hasPermission($permission)->shouldReturn(false);
        $this->addPermission($permission);
        $this->hasPermission($permission)->shouldReturn(true);
    }

    function it_can_remove_permissions(PermissionInterface $permission)
    {
        $this->addPermission($permission);
        $this->removePermission($permission);
        $this->hasPermission($permission)->shouldReturn(false);
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
