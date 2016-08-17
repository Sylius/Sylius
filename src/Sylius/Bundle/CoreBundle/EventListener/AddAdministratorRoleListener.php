<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Repository\RoleRepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class AddAdministratorRoleListener
{
    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \RuntimeException
     */
    public function addAdministrationRole(GenericEvent $event)
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $event->getSubject();

        /** @var RoleInterface $administratorRole */
        $administratorRole = $this->roleRepository->findOneBy(['code' => 'administrator']);

        if (null === $administratorRole) {
            throw new \RuntimeException('Cannot add administration role, because cannot find administrator role');
        }

        if (!$adminUser instanceof AdminUserInterface) {
            throw new \RuntimeException('Cannot add administration role, because given subject is not admin user');
        }

        $adminUser->addAuthorizationRole($administratorRole);
    }
}
